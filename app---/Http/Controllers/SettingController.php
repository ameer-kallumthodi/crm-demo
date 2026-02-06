<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AuthHelper;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // Get all settings in one query to reduce database calls
        $settings = Setting::whereIn('key', [
            'site_name', 'site_description', 'site_logo', 'site_favicon', 'bg_image'
        ])->pluck('value', 'key');

        $siteSettings = [
            'site_name' => $settings->get('site_name', 'Base CRM'),
            'site_description' => $settings->get('site_description', 'CRM Management System'),
            'site_logo' => $settings->get('site_logo', 'storage/logo.png'),
            'site_favicon' => $settings->get('site_favicon', 'storage/favicon.ico'),
            'bg_image' => $settings->get('bg_image', 'assets/mantis/images/auth-bg.jpg'),
        ];
        
        return view('admin.settings.index', compact('siteSettings'));
    }

    public function updateLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete old logo if exists
            if (Storage::disk('public')->exists('logo.png')) {
                Storage::disk('public')->delete('logo.png');
            }

            // Store new logo
            $logoPath = $request->file('logo')->storeAs('', 'logo.png', 'public');
            
            // Update settings table
            Setting::set('site_logo', 'storage/logo.png', 'file', 'Website logo file path', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Logo updated successfully!',
                'logo_url' => asset('storage/logo.png')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the logo. Please try again.'
            ], 500);
        }
    }

    public function updateFavicon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'favicon' => 'required|image|mimes:ico,png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete old favicon if exists
            if (Storage::disk('public')->exists('favicon.ico')) {
                Storage::disk('public')->delete('favicon.ico');
            }

            // Store new favicon
            $faviconPath = $request->file('favicon')->storeAs('', 'favicon.ico', 'public');
            
            // Update settings table
            Setting::set('site_favicon', 'storage/favicon.ico', 'file', 'Website favicon file path', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Favicon updated successfully!',
                'favicon_url' => asset('storage/favicon.ico')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the favicon. Please try again.'
            ], 500);
        }
    }

    public function updateSiteSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update site name
            Setting::set('site_name', $request->site_name, 'text', 'Website name displayed in title and header', 'site');
            
            // Update site description
            Setting::set('site_description', $request->site_description, 'text', 'Website description for SEO and meta tags', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Site settings updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating site settings. Please try again.'
            ], 500);
        }
    }


    public function updateBackgroundImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bg_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the errors below.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete old background image if exists
            if (Storage::disk('public')->exists('auth-bg.jpg')) {
                Storage::disk('public')->delete('auth-bg.jpg');
            }

            // Store new background image
            $bgImagePath = $request->file('bg_image')->storeAs('', 'auth-bg.jpg', 'public');
            
            // Update settings table
            Setting::set('bg_image', 'storage/auth-bg.jpg', 'file', 'Login page background image', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Background image updated successfully!',
                'bg_image_url' => asset('storage/auth-bg.jpg')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the background image. Please try again.'
            ], 500);
        }
    }

    public function removeBackgroundImage(Request $request)
    {
        try {
            // Delete the current background image file if it exists
            if (Storage::disk('public')->exists('auth-bg.jpg')) {
                Storage::disk('public')->delete('auth-bg.jpg');
            }
            
            // Update settings table to use default background
            Setting::set('bg_image', 'assets/mantis/images/auth-bg.jpg', 'file', 'Login page background image', 'site');

            return response()->json([
                'success' => true,
                'message' => 'Background image removed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the background image. Please try again.'
            ], 500);
        }
    }

}
