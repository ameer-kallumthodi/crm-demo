<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class AuthHelper
{
    /**
     * Get session value
     */
    public static function getSessionValue($sessionKey)
    {
        return Session::get($sessionKey);
    }

    /**
     * Get user ID from session
     */
    public static function getUserId()
    {
        return self::getSessionValue('user_id');
    }

    /**
     * Get role ID from session
     */
    public static function getRoleId()
    {
        return self::getSessionValue('role_id');
    }

    /**
     * Check if user is team lead
     */
    public static function isTeamLead()
    {
        return self::getSessionValue('is_team_lead');
    }

    /**
     * Check if user is team manager
     */
    public static function isTeamManager()
    {
        return self::getSessionValue('is_team_manager');
    }

    /**
     * Get role title from session
     */
    public static function getRoleTitle()
    {
        return self::getSessionValue('role_title');
    }

    /**
     * Get user name from session
     */
    public static function getUserName()
    {
        return self::getSessionValue('user_name');
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        $isLoggedIn = self::getSessionValue('is_logged_in');
        $isUserId = self::getUserId() > 0;
        return $isUserId && $isLoggedIn;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin()
    {
        return self::getRoleId() == 1;
    }

    /**
     * Check if user is telecaller
     */
    public static function isTelecaller()
    {
        return self::getRoleId() == 6;
    }

    /**
     * Check if user is accountant
     */
    public static function isAccountant()
    {
        return self::getRoleId() == 10;
    }

    /**
     * Check if user is administrator
     */
    public static function isAdministrator()
    {
        return self::getRoleId() == 11;
    }

    /**
     * Get team member IDs for a team
     */
    public static function getTeamMemberIds($teamId)
    {
        if (!$teamId) {
            return [];
        }

        $users = \App\Models\User::where('team_id', $teamId)
            ->where('role_id', 6)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        return $users;
    }

    /**
     * Get current authenticated user ID
     */
    public static function getCurrentUserId()
    {
        return self::getUserId();
    }

    /**
     * Get current authenticated user
     */
    public static function getCurrentUser()
    {
        $userId = self::getUserId();
        if (!$userId) {
            return null;
        }
        
        return \App\Models\User::find($userId);
    }
}
