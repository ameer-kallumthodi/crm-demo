<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;
use App\Helpers\AuthHelper;

class BoardController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
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
            'is_active' => 'boolean',
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
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if board is being used by any leads or converted leads
        if ($board->leads()->count() > 0 || $board->convertedLeads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete board. It is being used by existing leads or converted leads.'
            ], 422);
        }

        $board->delete();

        return response()->json([
            'success' => true,
            'message' => 'Board deleted successfully.'
        ]);
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

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:boards,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Board::create([
            'title' => $request->title,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'created_by' => AuthHelper::getCurrentUserId(),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return redirect()->route('admin.boards.index')->with('message_success', 'Board created successfully!');
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

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:boards,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $board = Board::findOrFail($id);
        $board->update([
            'title' => $request->title,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'updated_by' => AuthHelper::getCurrentUserId(),
        ]);

        return redirect()->route('admin.boards.index')->with('message_success', 'Board updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $board = Board::findOrFail($id);
        
        // Check if board has leads or converted leads
        if ($board->leads()->count() > 0 || $board->convertedLeads()->count() > 0) {
            return redirect()->route('admin.boards.index')->with('message_danger', 'Cannot delete board. It has assigned leads or converted leads.');
        }

        $board->delete();
        return redirect()->route('admin.boards.index')->with('message_success', 'Board deleted successfully!');
    }
}
