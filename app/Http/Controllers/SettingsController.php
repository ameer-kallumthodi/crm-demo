<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class SettingsController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $settings = Setting::orderBy('group')->orderBy('key')->get();
        $groups = Setting::distinct()->pluck('group')->toArray();
        
        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
            'type' => 'required|in:text,number,boolean,json,file',
            'description' => 'nullable|string',
            'group' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $setting = Setting::create([
            'key' => $request->key,
            'value' => $request->value,
            'type' => $request->type,
            'description' => $request->description,
            'group' => $request->group,
            'is_public' => $request->has('is_public'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Setting created successfully.',
            'data' => $setting
        ]);
    }

    public function show(Setting $setting)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($setting);
    }

    public function update(Request $request, Setting $setting)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'value' => 'nullable|string',
            'type' => 'required|in:text,number,boolean,json,file',
            'description' => 'nullable|string',
            'group' => 'required|string|max:255',
            'is_public' => 'boolean',
        ]);

        $setting->update([
            'key' => $request->key,
            'value' => $request->value,
            'type' => $request->type,
            'description' => $request->description,
            'group' => $request->group,
            'is_public' => $request->has('is_public'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully.',
            'data' => $setting
        ]);
    }

    public function destroy(Setting $setting)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully.'
        ]);
    }
}