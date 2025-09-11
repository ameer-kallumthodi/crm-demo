<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\UserRole;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function index()
    {
        // Check if user is already logged in using our custom AuthHelper
        if (\App\Helpers\AuthHelper::isLoggedIn()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;
        
        $loginResult = User::login($email, $password);

        if ($loginResult['status'] == 1) {
            $user = $loginResult['user'];
            $userRole = UserRole::find($user->role_id);
            
            // Set session data
            Session::put([
                'user_id' => $user->id,
                'is_team_lead' => $user->is_team_lead,
                'is_team_manager' => $user->is_team_manager,
                'role_id' => $user->role_id,
                'role_title' => $userRole ? $userRole->title : '',
                'user_name' => $user->name,
                'user_email' => $user->email,
                'is_logged_in' => true,
                'logged_in_at' => time(),
            ]);

            return redirect()->route('dashboard')
                ->with('success', "Welcome back! <b>{$user->name}</b>");
        } else {
            return back()->withErrors(['error' => $loginResult['message']]);
        }
    }

    /**
     * Logout the user.
     */
    public function logout()
    {
        Session::flush();
        
        return redirect()->route('login')
            ->with('message', 'Session expired, Login Again!');
    }
}
