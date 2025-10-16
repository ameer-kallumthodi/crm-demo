<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\Schema;

class BoardController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin() && !RoleHelper::is_admission_counsellor()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $boards = Board::with(['createdBy', 'updatedBy'])->orderBy('created_at', 'desc')->get();
        return view('admin.boards.index', compact('boards'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:boards,code',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $board = Board::create([
            'title' => $request->title,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Board created successfully.',
            'data' => $board
        ]);
    }

    public function show(Board $board)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($board);
    }

    public function destroy(Board $board)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $hasLeadBoard = Schema::hasColumn('leads', 'board_id');
        $hasConvertedLeadBoard = Schema::hasColumn('converted_leads', 'board_id');

        $hasRelatedLeads = false;
        $hasRelatedConvertedLeads = false;

        if ($hasLeadBoard) {
            $hasRelatedLeads = $board->leads()->count() > 0;
        }
        if ($hasConvertedLeadBoard) {
            $hasRelatedConvertedLeads = $board->convertedLeads()->count() > 0;
        }

        if ($hasRelatedLeads || $hasRelatedConvertedLeads) {
            if (request()->expectsJson()) {
                return response()->json([
                    'error' => 'Cannot delete board. It is being used by existing leads or converted leads.'
                ], 422);
            }
            return redirect()->route('admin.boards.index')->with('message_danger', 'Cannot delete board. It has assigned leads or converted leads.');
        }

        $board->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Board deleted successfully.'
            ]);
        }
        return redirect()->route('admin.boards.index')->with('message_success', 'Board deleted successfully!');
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.boards.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:boards,code',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $board = Board::create([
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'created_by' => AuthHelper::getCurrentUserId(),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.boards.index')->with('message_success', 'Board created successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to create board: ' . $e->getMessage());
        }
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = Board::findOrFail($id);
        return view('admin.boards.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        try {
            // Normalize checkbox to boolean before validation
            $request->merge([
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'required|string|max:10|unique:boards,code,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            $board = Board::findOrFail($id);
            $board->update([
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'updated_by' => AuthHelper::getCurrentUserId(),
            ]);

            return redirect()->route('admin.boards.index')->with('message_success', 'Board updated successfully!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('message_danger', 'Failed to update board: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
            }
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        \Log::info('[BoardController@delete] Attempting delete', ['id' => $id]);

        try {
        $board = Board::findOrFail($id);

        $hasLeadBoard = Schema::hasColumn('leads', 'board_id');
        $hasConvertedLeadBoard = Schema::hasColumn('converted_leads', 'board_id');

        $hasRelatedLeads = false;
        $hasRelatedConvertedLeads = false;

        if ($hasLeadBoard) {
            $hasRelatedLeads = $board->leads()->count() > 0;
        }
        if ($hasConvertedLeadBoard) {
            $hasRelatedConvertedLeads = $board->convertedLeads()->count() > 0;
        }

        if ($hasRelatedLeads || $hasRelatedConvertedLeads) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete board. It has assigned leads or converted leads.'
                    ], 422);
                }
                return redirect()->route('admin.boards.index')->with('message_danger', 'Cannot delete board. It has assigned leads or converted leads.');
            }

            $board->delete();
            \Log::info('[BoardController@delete] Board deleted', ['id' => $id]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Board deleted successfully!'
                ]);
            }
            return redirect()->route('admin.boards.index')->with('message_success', 'Board deleted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Board not found.'
                ], 404);
            }
            return redirect()->route('admin.boards.index')->with('message_danger', 'Board not found.');
        } catch (\Throwable $e) {
            \Log::error('[BoardController@delete] Error deleting board: ' . $e->getMessage(), ['id' => $id]);
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the board. Please try again.'
                ], 500);
            }
            return redirect()->route('admin.boards.index')->with('message_danger', 'An error occurred while deleting the board. Please try again.');
        }
    }
}
