<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConvertedLead;
use App\Models\Lead;
use App\Helpers\AuthHelper;
use App\Helpers\RoleHelper;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use App\Models\ConvertedLeadIdCard;
use Illuminate\Support\Facades\Mail;
use App\Mail\IdCardNotification;

class ConvertedLeadController extends Controller
{
    /**
     * Display a listing of converted leads
     */
    public function index(Request $request)
    {
        $query = ConvertedLead::with(['lead', 'course', 'academicAssistant', 'createdBy']);

        // Apply role-based filtering
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            // Check team lead first (highest priority)
            if (RoleHelper::is_team_lead()) {
                // Team Lead: Can see converted leads from their team
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    $query->whereIn('created_by', $teamMemberIds);
                } else {
                    // If no team assigned, only show their own leads
                    $query->where('created_by', AuthHelper::getCurrentUserId());
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Admission Counsellor: Can see ALL converted leads
                // No additional filtering needed - show all
            } elseif (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can only see converted leads assigned to them
                $query->where('academic_assistant_id', AuthHelper::getCurrentUserId());
            } elseif (RoleHelper::is_telecaller()) {
                // Telecaller: Can only see converted leads they created
                $query->where('created_by', AuthHelper::getCurrentUserId());
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }


        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $convertedLeads = $query->orderBy('created_at', 'desc')->get();

        // Get filter data
        $courses = \App\Models\Course::where('is_active', 1)->get();

        return view('admin.converted-leads.index', compact('convertedLeads', 'courses'));
    }

    /**
     * Display the specified converted lead
     */
    public function show($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy'
        ])->findOrFail($id);

        // Apply role-based access control
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            // Check team lead first (highest priority)
            if (RoleHelper::is_team_lead()) {
                // Team Lead: Can see converted leads from their team
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    if (!in_array($convertedLead->created_by, $teamMemberIds)) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads from your team.');
                    }
                } else {
                    // If no team assigned, only show their own leads
                    if ($convertedLead->created_by != AuthHelper::getCurrentUserId()) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads you created.');
                    }
                }
            } elseif (RoleHelper::is_admission_counsellor()) {
                // Admission Counsellor: Can see ALL converted leads
                // No additional filtering needed
            } elseif (RoleHelper::is_academic_assistant()) {
                // Academic Assistant: Can only see converted leads assigned to them
                if ($convertedLead->academic_assistant_id != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads assigned to you.');
                }
            } elseif (RoleHelper::is_telecaller()) {
                // Telecaller: Can only see converted leads they created
                if ($convertedLead->created_by != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads you created.');
                }
            }
        }

        // Get lead activities for this converted lead
        $leadActivities = \App\Models\LeadActivity::where('lead_id', $convertedLead->lead_id)
            ->select('id', 'lead_id', 'reason', 'created_at', 'activity_type', 'description', 'remarks', 'rating', 'followup_date', 'created_by', 'lead_status_id')
            ->with(['leadStatus:id,title', 'createdBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.converted-leads.show', compact('convertedLead', 'leadActivities'));
    }


    public function generateIdCardPdf($id)
    {
        $convertedLead = ConvertedLead::with([
            'lead',
            'leadDetail',
            'course',
            'academicAssistant',
            'createdBy'
        ])->findOrFail($id);

        // Role-based access (same logic as you had)
        $currentUser = AuthHelper::getCurrentUser();
        if ($currentUser) {
            if (RoleHelper::is_team_lead()) {
                $teamId = $currentUser->team_id;
                if ($teamId) {
                    $teamMemberIds = \App\Models\User::where('team_id', $teamId)->pluck('id')->toArray();
                    if (!in_array($convertedLead->created_by, $teamMemberIds)) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads from your team.');
                    }
                } else {
                    if ($convertedLead->created_by != AuthHelper::getCurrentUserId()) {
                        return redirect()->route('admin.converted-leads.index')
                            ->with('message_danger', 'Access denied. You can only view converted leads you created.');
                    }
                }
            } elseif (RoleHelper::is_academic_assistant()) {
                if ($convertedLead->academic_assistant_id != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads assigned to you.');
                }
            } elseif (RoleHelper::is_telecaller()) {
                if ($convertedLead->created_by != AuthHelper::getCurrentUserId()) {
                    return redirect()->route('admin.converted-leads.index')
                        ->with('message_danger', 'Access denied. You can only view converted leads you created.');
                }
            }
            // Admission counsellor = can see all
        }

        // Create circular image if passport photo exists
        $circularImagePath = null;
        if ($convertedLead->leadDetail && $convertedLead->leadDetail->passport_photo) {
            $circularImagePath = $this->createCircularImage($convertedLead->leadDetail->passport_photo);
        }

        // Load Blade view
        $html = view('admin.converted-leads.id-card-pdf', compact('convertedLead', 'circularImagePath'))->render();

        // Create mPDF instance
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);

        // Write HTML
        $mpdf->WriteHTML($html);

        $filename = 'id_card_' . $convertedLead->name . '_' . $convertedLead->id . '.pdf';

        // Stream to browser
        return response($mpdf->Output($filename, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function generateAndStoreIdCard($id)
    {
        $convertedLead = ConvertedLead::with(['lead','leadDetail','course','academicAssistant','createdBy'])
            ->findOrFail($id);

        // Check if ID card was already generated recently (within last 30 seconds)
        $recentIdCard = ConvertedLeadIdCard::where('converted_lead_id', $id)
            ->where('generated_at', '>', now()->subSeconds(30))
            ->first();
            
        if ($recentIdCard) {
            return response()->json([
                'success' => false,
                'message' => 'ID card was already generated recently. Please wait a moment before generating again.',
            ], 429);
        }

        // Create circular image if passport photo exists
        $circularImagePath = null;
        if ($convertedLead->leadDetail && $convertedLead->leadDetail->passport_photo) {
            $circularImagePath = $this->createCircularImage($convertedLead->leadDetail->passport_photo);
        }

        $html = view('admin.converted-leads.id-card-pdf', compact('convertedLead', 'circularImagePath'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);

        $mpdf->WriteHTML($html);

        $safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $convertedLead->name);
        $fileName = 'id_card_' . $safeName . '_' . $convertedLead->id . '_' . time() . '.pdf';
        $relativePath = 'id_cards/' . $fileName;

        // Ensure directory exists
        if (!Storage::disk('public')->exists('id_cards')) {
            Storage::disk('public')->makeDirectory('id_cards');
        }

        // Save PDF to storage/app/public/id_cards
        $pdfContent = $mpdf->Output($fileName, 'S');
        Storage::disk('public')->put($relativePath, $pdfContent);

        // Create DB record (upsert latest per converted lead)
        $idCardRecord = ConvertedLeadIdCard::updateOrCreate(
            ['converted_lead_id' => $convertedLead->id],
            [
                'file_path' => 'storage/' . $relativePath,
                'file_name' => $fileName,
                'generated_at' => now(),
                'generated_by' => AuthHelper::getCurrentUserId(),
            ]
        );

        // Send email to student with ID card attachment
        try {
            if ($convertedLead->email) {
                Mail::to($convertedLead->email)->send(new IdCardNotification(
                    $convertedLead->name,
                    $convertedLead->course ? $convertedLead->course->title : 'N/A',
                    $idCardRecord->file_path
                ));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send ID card email: ' . $e->getMessage());
            // Continue execution even if email fails
        }

        return response()->json([
            'success' => true,
            'message' => 'ID Card generated, stored, and sent to student email successfully.',
        ]);
    }

    public function viewStoredIdCard($id)
    {
        $convertedLead = ConvertedLead::findOrFail($id);
        $record = ConvertedLeadIdCard::where('converted_lead_id', $convertedLead->id)->first();
        if (!$record) {
            return redirect()->back()->with('message_danger', 'ID Card not generated yet.');
        }

        $absolute = public_path($record->file_path);
        if (!file_exists($absolute)) {
            return redirect()->back()->with('message_danger', 'Stored ID Card file missing.');
        }

        return response()->file($absolute, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    /**
     * Show modal for updating register number
     */
    public function showUpdateRegisterNumberModal($id)
    {
        $convertedLead = ConvertedLead::findOrFail($id);
        
        // Check if user is admin or super admin
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied. Only admins can update register numbers.'], 403);
        }

        return view('admin.converted-leads.update-register-number-modal', compact('convertedLead'));
    }

    /**
     * Update register number
     */
    public function updateRegisterNumber(Request $request, $id)
    {
        // Check if user is admin or super admin
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied. Only admins can update register numbers.'], 403);
        }

        $request->validate([
            'register_number' => 'required|string|max:50|unique:converted_leads,register_number,' . $id
        ]);

        $convertedLead = ConvertedLead::findOrFail($id);
        
        $convertedLead->update([
            'register_number' => $request->register_number,
            'reg_updated_at' => now(),
            'reg_updated_by' => AuthHelper::getCurrentUserId()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Register number updated successfully.',
            'register_number' => $convertedLead->register_number
        ]);
    }

    /**
     * Create a circular version of the passport photo
     */
    private function createCircularImage($imagePath)
    {
        try {
            $originalPath = public_path('storage/' . $imagePath);
            
            if (!file_exists($originalPath)) {
                return null;
            }

            // Get image info
            $imageInfo = getimagesize($originalPath);
            $mimeType = $imageInfo['mime'];
            
            // Create image resource based on type
            switch ($mimeType) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($originalPath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($originalPath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($originalPath);
                    break;
                default:
                    return null;
            }

            // Set dimensions
            $size = 200;
            $radius = $size / 2;

            // Create a new image with transparent background
            $circular = imagecreatetruecolor($size, $size);
            imagealphablending($circular, false);
            imagesavealpha($circular, true);
            $transparent = imagecolorallocatealpha($circular, 0, 0, 0, 127);
            imagefill($circular, 0, 0, $transparent);

            // Create circular mask
            $mask = imagecreatetruecolor($size, $size);
            imagealphablending($mask, false);
            imagesavealpha($mask, true);
            imagefill($mask, 0, 0, $transparent);

            // Draw white circle for mask
            $white = imagecolorallocate($mask, 255, 255, 255);
            imagefilledellipse($mask, $radius, $radius, $size, $size, $white);

            // Apply mask to source image
            imagealphablending($source, true);
            imagealphablending($circular, true);
            
            // Copy and resize source image
            imagecopyresampled($circular, $source, 0, 0, 0, 0, $size, $size, imagesx($source), imagesy($source));
            
            // Apply circular mask
            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    $color = imagecolorat($mask, $x, $y);
                    if ($color == 0) { // Black pixels in mask
                        imagesetpixel($circular, $x, $y, $transparent);
                    }
                }
            }

            // Save circular image
            $circularPath = 'temp/circular_' . uniqid() . '.png';
            $fullCircularPath = public_path($circularPath);
            
            // Create temp directory if it doesn't exist
            if (!file_exists(public_path('temp'))) {
                mkdir(public_path('temp'), 0755, true);
            }
            
            imagepng($circular, $fullCircularPath);
            
            // Clean up
            imagedestroy($source);
            imagedestroy($circular);
            imagedestroy($mask);
            
            return $circularPath;
            
        } catch (\Exception $e) {
            \Log::error('Error creating circular image: ' . $e->getMessage());
            return null;
        }
    }
}
