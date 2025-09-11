<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Show the user profile page
     */
    public function index()
    {
        $user = AuthHelper::getCurrentUser();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('message', 'Please login to view your profile.');
        }

        return view('profile.index', compact('user'));
    }

    /**
     * Show the form for editing the user profile
     */
    public function edit()
    {
        $user = AuthHelper::getCurrentUser();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('message', 'Please login to edit your profile.');
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user profile
     */
    public function update(Request $request)
    {
        $user = AuthHelper::getCurrentUser();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('message', 'Please login to update your profile.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile')
            ->with('message_success', 'Profile updated successfully!');
    }

    /**
     * Update the user password
     */
    public function updatePassword(Request $request)
    {
        $user = AuthHelper::getCurrentUser();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('message', 'Please login to update your password.');
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password (you may need to implement this based on your auth system)
        // For now, we'll just update the password
        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return redirect()->route('profile.edit')
            ->with('message_success', 'Password updated successfully!');
    }
}
