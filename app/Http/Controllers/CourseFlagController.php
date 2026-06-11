<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\CourseFlag;
use Illuminate\Http\Request;

class CourseFlagController extends Controller
{
    public function listActive()
    {
        $courseFlags = CourseFlag::orderBy('title')->get(['id', 'title', 'description', 'color']);

        return response()->json($courseFlags);
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

        $courseFlags = CourseFlag::orderBy('title')->get();

        return view('admin.course-flags.index', compact('courseFlags'));
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

        $courseFlag = CourseFlag::findOrFail($id);
        $courseFlag->update([
            'color' => $request->color,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Course Flag updated successfully.',
                'data' => $courseFlag,
            ]);
        }

        return redirect()->route('admin.course-flags.index')->with('message_success', 'Course Flag updated successfully!');
    }

    public function ajax_add()
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        return view('admin.course-flags.add');
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

        $courseFlag = CourseFlag::where('color', $validated['color'])
            ->where('title', trim((string) $validated['title']))
            ->where('description', trim((string) $validated['description']))
            ->first();
        $wasCreated = false;

        if (! $courseFlag) {
            $courseFlag = CourseFlag::create([
                'color' => $validated['color'],
                'title' => trim((string) $validated['title']),
                'description' => trim((string) $validated['description']),
            ]);
            $wasCreated = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $wasCreated ? 'Course Flag created successfully!' : 'Same course flag already exists.',
                'data' => $courseFlag,
            ]);
        }

        return redirect()->route('admin.course-flags.index')->with(
            'message_success',
            $wasCreated ? 'Course Flag created successfully!' : 'Same course flag already exists.'
        );
    }

    public function ajax_edit($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $edit_data = CourseFlag::findOrFail($id);

        return view('admin.course-flags.edit', compact('edit_data'));
    }

    public function delete($id)
    {
        if (! $this->canManage()) {
            return redirect()->route('dashboard')->with('message_danger', 'Access denied.');
        }

        $courseFlag = CourseFlag::findOrFail($id);
        $courseFlag->delete();

        return redirect()->route('admin.course-flags.index')->with('message_success', 'Course Flag deleted successfully!');
    }
}
