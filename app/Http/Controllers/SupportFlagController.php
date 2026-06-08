<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\SupportFlag;
use Illuminate\Http\Request;

class SupportFlagController extends Controller
{
    public function listActive()
    {
        $supportFlags = SupportFlag::orderBy('title')->get(['id', 'title', 'description', 'color']);

        return response()->json($supportFlags);
    }

    private function canManage(): bool
    {
        return PermissionHelper::can_manage_subject_areas_mails_flags();
    }

    private function baseRules(): array
    {
        return [
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }

    public function index()
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $supportFlags = SupportFlag::orderBy('title')->get();

        return view('admin.support-flags.index', compact('supportFlags'));
    }

    public function update(Request $request, $id)
    {
        if (! $this->canManage()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $request->validate($this->baseRules());

        $supportFlag = SupportFlag::findOrFail($id);
        $supportFlag->update([
            'color' => $request->color,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Support Flag updated successfully.',
                'data' => $supportFlag,
            ]);
        }

        return redirect()->route('admin.support-flags.index')->with('message_success', 'Support Flag updated successfully!');
    }

    public function ajax_add()
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.support-flags.add');
    }

    public function submit(Request $request)
    {
        if (! $this->canManage()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Access denied.'], 403);
            }

            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $validated = $request->validate($this->baseRules());

        $supportFlag = SupportFlag::where('color', $validated['color'])
            ->where('title', trim((string) $validated['title']))
            ->where('description', trim((string) $validated['description']))
            ->first();
        $wasCreated = false;

        if (! $supportFlag) {
            $supportFlag = SupportFlag::create([
                'color' => $validated['color'],
                'title' => trim((string) $validated['title']),
                'description' => trim((string) $validated['description']),
            ]);
            $wasCreated = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $wasCreated ? 'Support Flag created successfully!' : 'Same support flag already exists.',
                'data' => $supportFlag,
            ]);
        }

        return redirect()->route('admin.support-flags.index')->with(
            'message_success',
            $wasCreated ? 'Support Flag created successfully!' : 'Same support flag already exists.'
        );
    }

    public function ajax_edit($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = SupportFlag::findOrFail($id);

        return view('admin.support-flags.edit', compact('edit_data'));
    }

    public function delete($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $supportFlag = SupportFlag::findOrFail($id);
        $supportFlag->delete();

        return redirect()->route('admin.support-flags.index')->with('message_success', 'Support Flag deleted successfully!');
    }
}
