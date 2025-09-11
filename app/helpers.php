<?php

if (!function_exists('has_permission')) {
    /**
     * Check if user has permission for a specific action
     */
    function has_permission($permission = '')
    {
        return \App\Helpers\PermissionHelper::has_permission($permission);
    }
}

if (!function_exists('can_access_menu')) {
    /**
     * Check if user can access a specific menu item
     */
    function can_access_menu($menu_permission)
    {
        return \App\Helpers\PermissionHelper::can_access_menu($menu_permission);
    }
}


if (!function_exists('is_super_admin')) {
    /**
     * Check if current user is Super Admin
     */
    function is_super_admin()
    {
        return \App\Helpers\RoleHelper::is_super_admin();
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is Admin
     */
    function is_admin()
    {
        return \App\Helpers\RoleHelper::is_admin();
    }
}

if (!function_exists('is_telecaller')) {
    /**
     * Check if current user is Telecaller
     */
    function is_telecaller()
    {
        return \App\Helpers\RoleHelper::is_telecaller();
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in()
    {
        return \App\Helpers\RoleHelper::is_logged_in();
    }
}

if (!function_exists('get_country_code')) {
    /**
     * Get country codes array
     */
    function get_country_code()
    {
        return \App\Helpers\CountriesHelper::get_country_code();
    }
}
