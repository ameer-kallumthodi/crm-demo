<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoxbayCallLog;
use App\Models\Lead;
use App\Models\User;
use App\Helpers\AuthHelper;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

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

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('agent_number')) {
            $query->where('AgentNumber', 'like', '%' . $request->agent_number . '%');
        }

        if ($request->filled('destination_number')) {
            $query->where('destinationNumber', 'like', '%' . $request->destination_number . '%');
        }

        // Get call logs with pagination
        $callLogs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Add telecaller names to each call log
        foreach ($callLogs as $callLog) {
            $callLog->telecaller_name = $callLog->getTelecallerName();
        }

        return view('admin.call-logs.index', compact('callLogs'));
    }

    /**
     * Display call logs for a specific lead
     */
    public function list($leadId): View
    {
        $lead = Lead::findOrFail($leadId);
        
        // Get call logs for this lead's phone number
        $callLogs = VoxbayCallLog::where(function($query) use ($lead) {
            $fullPhone = $lead->code . $lead->phone;
            $query->where('destinationNumber', $fullPhone)
                  ->orWhere('calledNumber', $fullPhone);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        // Add telecaller names to each call log
        foreach ($callLogs as $callLog) {
            $callLog->telecaller_name = $callLog->getTelecallerName();
        }

        return view('admin.call-logs.list', compact('callLogs', 'lead'));
    }

    /**
     * Display the specified call log
     */
    public function show(VoxbayCallLog $callLog): View
    {
        $callLog->load(['createdBy', 'updatedBy']);
        $callLog->telecaller_name = $callLog->getTelecallerName();
        $callLog->lead = $callLog->getLeadByPhone();

        return view('admin.call-logs.show', compact('callLog'));
    }

    /**
     * Get call logs via AJAX
     */
    public function ajaxList(Request $request): JsonResponse
    {
        try {
            $query = VoxbayCallLog::query();

            // Apply filters
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->where('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('date', '<=', $request->date_to);
            }

            if ($request->filled('agent_number')) {
                $query->where('AgentNumber', 'like', '%' . $request->agent_number . '%');
            }

            if ($request->filled('destination_number')) {
                $query->where('destinationNumber', 'like', '%' . $request->destination_number . '%');
            }

            $callLogs = $query->orderBy('created_at', 'desc')
                            ->limit(50)
                            ->get();

            // Add telecaller names and lead information
            foreach ($callLogs as $callLog) {
                $callLog->telecaller_name = $callLog->getTelecallerName();
                $callLog->lead = $callLog->getLeadByPhone();
            }

            return response()->json([
                'status' => 'success',
                'data' => $callLogs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch call logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get call statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $query = VoxbayCallLog::query();

            // Apply date range filter
            if ($request->filled('date_from')) {
                $query->where('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('date', '<=', $request->date_to);
            }

            $statistics = [
                'total_calls' => $query->count(),
                'incoming_calls' => $query->clone()->incoming()->count(),
                'outgoing_calls' => $query->clone()->outgoing()->count(),
                'missed_calls' => $query->clone()->missedCall()->count(),
                'answered_calls' => $query->clone()->where('status', 'ANSWER')->count(),
                'cancelled_calls' => $query->clone()->where('status', 'CANCEL')->count(),
                'busy_calls' => $query->clone()->where('status', 'BUSY')->count(),
            ];

            // Get calls by date for chart
            $callsByDate = $query->clone()
                ->selectRaw('date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'status' => 'success',
                'statistics' => $statistics,
                'calls_by_date' => $callsByDate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a call log
     */
    public function destroy(VoxbayCallLog $callLog): JsonResponse
    {
        try {
            $callLog->deleted_by = AuthHelper::getCurrentUserId();
            $callLog->save();
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
