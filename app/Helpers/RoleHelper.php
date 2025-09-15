<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserRole;

class RoleHelper
{
    /**
     * Check if user is logged in
     */
    public static function is_logged_in()
    {
        return AuthHelper::isLoggedIn();
    }

    /**
     * Check if current user is Super Admin
     */
    public static function is_super_admin()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        $role = UserRole::find($user->role_id);
        return $role && $role->title === 'Super Admin';
    }

    /**
     * Check if current user is Admin
     */
    public static function is_admin()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        $role = UserRole::find($user->role_id);
        return $role && $role->title === 'Admin';
    }

    /**
     * Check if current user is Telecaller
     */
    public static function is_telecaller()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        $role = UserRole::find($user->role_id);
        return $role && $role->title === 'Telecaller';
    }

    /**
     * Check if current user is Team Lead
     */
    public static function is_team_lead()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->is_team_lead == 1;
    }

    /**
     * Check if current user is Admission Counsellor
     */
    public static function is_admission_counsellor()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 4;
    }

    /**
     * Check if current user is Academic Assistant
     */
    public static function is_academic_assistant()
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        return $user->role_id == 5;
    }


    /**
     * Check if current user has admin or super admin role
     */
    public static function is_admin_or_super_admin()
    {
        return self::is_admin() || self::is_super_admin();
    }

    /**
     * Get current user's role title
     */
    public static function get_current_user_role()
    {
        if (!self::is_logged_in()) {
            return null;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return null;
        }

        $role = UserRole::find($user->role_id);
        return $role ? $role->title : null;
    }

    /**
     * Check if user has specific role
     */
    public static function has_role($roleTitle)
    {
        if (!self::is_logged_in()) {
            return false;
        }

        $user = AuthHelper::getCurrentUser();
        if (!$user) {
            return false;
        }

        $role = UserRole::find($user->role_id);
        return $role && $role->title === $roleTitle;
    }
}
