<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VoxbayCallLog;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VoxbayCallLogController extends Controller
{
    /**
     * Display a listing of call logs
     */
    public function index(Request $request): View
    {
        $query = VoxbayCallLog::with(['createdBy', 'updatedBy']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('agent_number')) {
            $query->where('AgentNumber', 'like', '%' . $request->agent_number . '%');
        }

        if ($request->filled('destination_number')) {
            $query->where('destinationNumber', 'like', '%' . $request->destination_number . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $callLogs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Add telecaller names to each call log
        $callLogs->getCollection()->transform(function ($callLog) {
            $callLog->telecaller_name = $callLog->getTelecallerName();
            return $callLog;
        });

        return view('admin.call-logs.index', compact('callLogs'));
    }

    /**
     * Display call logs for a specific lead
     */
    public function list(Request $request, $leadId): View
    {
        $lead = Lead::findOrFail($leadId);

        $query = VoxbayCallLog::where(function ($q) use ($lead) {
            $fullPhone = $lead->code . $lead->phone;
            $q->where('destinationNumber', $fullPhone)
              ->orWhere('calledNumber', $fullPhone);
        });

        // Apply additional filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $callLogs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Add telecaller names to each call log
        $callLogs->getCollection()->transform(function ($callLog) {
            $callLog->telecaller_name = $callLog->getTelecallerName();
            return $callLog;
        });

        return view('admin.call-logs.list', compact('callLogs', 'lead'));
    }

    /**
     * Display the specified call log
     */
    public function show(VoxbayCallLog $callLog): View
    {
        $callLog->telecaller_name = $callLog->getTelecallerName();
        $lead = $callLog->getLeadByPhone();

        return view('admin.call-logs.show', compact('callLog', 'lead'));
    }

    /**
     * AJAX endpoint for call logs list
     */
    public function ajaxList(Request $request): JsonResponse
    {
        $query = VoxbayCallLog::with(['createdBy', 'updatedBy']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('agent_number')) {
            $query->where('AgentNumber', 'like', '%' . $request->agent_number . '%');
        }

        if ($request->filled('destination_number')) {
            $query->where('destinationNumber', 'like', '%' . $request->destination_number . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $callLogs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Add telecaller names to each call log
        $callLogs->getCollection()->transform(function ($callLog) {
            $callLog->telecaller_name = $callLog->getTelecallerName();
            return $callLog;
        });

        return response()->json([
            'status' => 'success',
            'data' => $callLogs
        ]);
    }

    /**
     * Get call logs statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = VoxbayCallLog::query();

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $stats = [
            'total_calls' => $query->count(),
            'incoming_calls' => $query->clone()->where('type', 'incoming')->count(),
            'outgoing_calls' => $query->clone()->where('type', 'outgoing')->count(),
            'missed_calls' => $query->clone()->where('type', 'missedcall')->count(),
            'answered_calls' => $query->clone()->where('status', 'ANSWER')->count(),
            'busy_calls' => $query->clone()->where('status', 'BUSY')->count(),
            'cancelled_calls' => $query->clone()->where('status', 'CANCEL')->count(),
            'no_answer_calls' => $query->clone()->where('status', 'NO ANSWER')->count(),
            'total_duration' => $query->clone()->sum('duration'),
            'average_duration' => $query->clone()->avg('duration'),
        ];

        // Get top telecallers by call count
        $topTelecallers = $query->clone()
            ->select('AgentNumber', DB::raw('COUNT(*) as call_count'))
            ->whereNotNull('AgentNumber')
            ->groupBy('AgentNumber')
            ->orderBy('call_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $countryCode = substr($item->AgentNumber, 0, 2);
                $mobileNumber = substr($item->AgentNumber, 2);
                
                $user = User::where('code', $countryCode)
                           ->where('phone', $mobileNumber)
                           ->whereHas('role', function($query) {
                               $query->where('title', 'Telecaller');
                           })
                           ->first();
                
                return [
                    'agent_number' => $item->AgentNumber,
                    'telecaller_name' => $user ? $user->name : 'Unknown',
                    'call_count' => $item->call_count
                ];
            });

        $stats['top_telecallers'] = $topTelecallers;

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Remove the specified call log
     */
    public function destroy(VoxbayCallLog $callLog): JsonResponse
    {
        try {
            $callLog->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Call log deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete call log: ' . $e->getMessage()
            ], 500);
        }
    }
}