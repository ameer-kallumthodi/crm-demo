<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Helpers\RoleHelper;

class CountryController extends Controller
{
    public function index()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $countries = Country::all();
        return view('admin.countries.index', compact('countries'));
    }

    public function store(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:countries,code',
            'phone_code' => 'required|string|max:10',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'boolean',
        ]);

        $country = Country::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'phone_code' => $request->phone_code,
            'currency' => $request->currency,
            'is_active' => $request->has('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Country created successfully.',
            'data' => $country
        ]);
    }

    public function show(Country $country)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json($country);
    }

    public function update(Request $request, Country $country)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:countries,code,' . $country->id,
            'phone_code' => 'required|string|max:10',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'boolean',
        ]);

        $country->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'phone_code' => $request->phone_code,
            'currency' => $request->currency,
            'is_active' => $request->has('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Country updated successfully.',
            'data' => $country
        ]);
    }

    public function destroy(Country $country)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Check if country is being used by any leads
        if ($country->leads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete country. It is being used by existing leads.'
            ], 422);
        }

        $country->delete();

        return response()->json([
            'success' => true,
            'message' => 'Country deleted successfully.'
        ]);
    }

    public function ajax_add()
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        return view('admin.countries.add');
    }

    public function submit(Request $request)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:countries,code',
            'phone_code' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        Country::create([
            'title' => $request->title,
            'code' => strtoupper($request->code),
            'phone_code' => $request->phone_code,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.countries.index')->with('message_success', 'Country created successfully!');
    }

    public function ajax_edit($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $edit_data = Country::findOrFail($id);
        return view('admin.countries.edit', compact('edit_data'));
    }

    public function update(Request $request, $id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:countries,code,' . $id,
            'phone_code' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        $country = Country::findOrFail($id);
        $country->update([
            'title' => $request->title,
            'code' => strtoupper($request->code),
            'phone_code' => $request->phone_code,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.countries.index')->with('message_success', 'Country updated successfully!');
    }

    public function delete($id)
    {
        if (!RoleHelper::is_admin_or_super_admin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $country = Country::findOrFail($id);
        
        // Check if country has leads
        if ($country->leads()->count() > 0) {
            return redirect()->route('admin.countries.index')->with('message_error', 'Cannot delete country. It has assigned leads.');
        }

        $country->delete();
        return redirect()->route('admin.countries.index')->with('message_success', 'Country deleted successfully!');
    }
}