<?php

namespace App\Helpers;

use App\Helpers\RoleHelper;

use function Symfony\Component\VarDumper\Dumper\esc;

class PermissionHelper
{
    /**
     * Check if user has permission for a specific page
     */
    public static function has_permission($permission = '')
    {
        // Check if super admin
        if (RoleHelper::is_super_admin()) {
            return self::has_permission_super_admin($permission);
        } elseif (RoleHelper::is_admin()) {
            return self::has_permission_admin($permission);
        } elseif (RoleHelper::is_telecaller()) {
            return self::has_permission_telecaller($permission);
        } elseif (RoleHelper::is_admission_counsellor()) {
            return self::has_permission_admission_counsellor($permission);
        } elseif (RoleHelper::is_academic_assistant()) {
            return self::has_permission_academic_assistant($permission);
        } elseif (RoleHelper::is_finance()) {
            return self::has_permission_finance($permission);
        } elseif (RoleHelper::is_post_sales()) {
            return self::has_permission_post_sales($permission);
        } elseif (RoleHelper::is_support_team()) {
            return self::has_permission_support_team($permission);
        } elseif (RoleHelper::is_general_manager()) {
            return self::has_permission_general_manager($permission);
		} elseif (RoleHelper::is_mentor()) {
			return self::has_permission_mentor($permission);
        }
        
        return false;
    }

    public static function has_lead_action_permission()
    {
        if (RoleHelper::is_admin_or_super_admin()) {
            return true;
        } elseif (RoleHelper::is_academic_assistant()) {
            return false;
        } elseif (RoleHelper::is_admission_counsellor()) {
            return false;
        } elseif (RoleHelper::is_finance()) {
            return false;
        } elseif (RoleHelper::is_post_sales()) {
            return false;
        } elseif (RoleHelper::is_telecaller()) {
            return true;
        } elseif (RoleHelper::is_general_manager()) {
            return true;
        }
        
        return false;
    }

    /**
     * Super Admin permissions - has access to everything
     */
    public static function has_permission_super_admin($permission = '')
    {
        $permissions = [
        ];
        return !in_array($permission, $permissions);
    }


    /**
     * Admin permissions
     */
    public static function has_permission_admin($permission = '')
    {
        $permissions = [
            'admin/settings/index',
            'admin/website/settings',
        ];
        return !in_array($permission, $permissions);
    }

    /**
     * Telecaller permissions
     */
    public static function has_permission_telecaller($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'leads/followup',
            'leads/registration-form-submitted',
            'admin/converted-leads/index',
            'admin/reports/leads',
            'profile/index',
        ];
        return in_array($permission, $permissions);
    }

    /**
     * Admission Counsellor permissions
     */
    public static function has_permission_admission_counsellor($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/notifications/index',
            'admin/reports/course-summary',
            'admin/courses/index',
            'admin/subjects/index',
            'admin/course-documents/index',
            'admin/universities/index',
            'admin/registration-links/index',
            'admin/teams/index',
            'admin/countries/index',
            'admin/boards/index',
            'admin/batches/index',
            'admin/admission-batches/index',
            'leads/registration-form-submitted',
        ];
        return in_array($permission, $permissions);
    }

    /**
     * Academic Assistant permissions
     */
    public static function has_permission_academic_assistant($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/notifications/index',
            'admin/universities/index',
            'leads/registration-form-submitted',
        ];
        return in_array($permission, $permissions);
    }

    /**
     * Academic Assistant permissions
     */
    public static function has_permission_finance($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
        ];
        return in_array($permission, $permissions);
    }

    /**
     * Post Sales permissions
     */
    public static function has_permission_post_sales($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
        ];
        return in_array($permission, $permissions);
    }

    /**
     * Support Team permissions
     */
    public static function has_permission_support_team($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'profile/index',
            'admin/converted-leads/index',
            'admin/support-team/index',
            'admin/support-bosse-converted-leads/index',
            'admin/support-nios-converted-leads/index',
            'admin/support-hotel-management-converted-leads/index',
            'admin/support-gmvss-converted-leads/index',
            'admin/support-ai-python-converted-leads/index',
            'admin/support-digital-marketing-converted-leads/index',
            'admin/support-ai-automation-converted-leads/index',
            'admin/support-web-development-converted-leads/index',
            'admin/support-vibe-coding-converted-leads/index',
            'admin/support-graphic-designing-converted-leads/index',
            'admin/support-eduthanzeel-converted-leads/index',
            'admin/support-e-school-converted-leads/index',
        ];
        return in_array($permission, $permissions);
    }

    /**
     * General Manager permissions
     */
    public static function has_permission_general_manager($permission = '')
    {
        $permissions = [
            'dashboard/index',
            'leads/index',
            'leads/followup',
            'leads/registration-form-submitted',
            'admin/converted-leads/index',
            'admin/reports/leads',
            'admin/general-managers/index',
            // User Management (index pages; actions are guarded in controllers)
            'admin/telecallers/index',
            'admin/post-sales/index',
            'profile/index',
        ];
        return in_array($permission, $permissions);
    }

	/**
	 * Mentor permissions
	 */
	public static function has_permission_mentor($permission = '')
	{
		$permissions = [
			'dashboard/index',
			'leads/index',
			'admin/converted-leads/index',
            'profile/index',
		];
		return in_array($permission, $permissions);
	}


    /**
     * Get app permissions for a specific role
     */
    public static function has_permission_app($role_id, $is_team_lead = 0, $is_team_manager = 0, $current_role = '')
    {
        if ($role_id == 1) { // Super Admin
            return self::get_permission_super_admin_app();
        } elseif ($role_id == 2) { // Admin
            return self::get_permission_admin_app();
        } elseif ($role_id == 3) { // Telecaller
            if ($is_team_manager == 1) {
                if ($current_role == 'telecaller') {
                    return self::get_permission_telecaller_app();
                } else {
                    return self::get_permission_team_manager_app();
                }
            } elseif ($is_team_lead == 1) {
                if ($current_role == 'telecaller') {
                    return self::get_permission_telecaller_app();
                } else {
                    return self::get_permission_team_lead_app();
                }
            } else {
                return self::get_permission_telecaller_app();
            }
        }
        
        return self::get_permission_telecaller_app(); // Default to telecaller permissions
    }

    /**
     * Super Admin app permissions
     */
    public static function get_permission_super_admin_app()
    {
        return [
            [
                'teams' => 1,
                'members' => 1,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 1,
                'enrollments' => 1,
                'admin_panel' => 1,
                'user_roles' => 1,
                'reports' => 1,
            ]
        ];
    }

    /**
     * Admin app permissions
     */
    public static function get_permission_admin_app()
    {
        return [
            [
                'teams' => 1,
                'members' => 1,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 0,
                'enrollments' => 1,
                'admin_panel' => 1,
                'user_roles' => 0,
                'reports' => 1,
            ]
        ];
    }

    /**
     * Team Manager app permissions
     */
    public static function get_permission_team_manager_app()
    {
        return [
            [
                'teams' => 1,
                'members' => 1,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 0,
                'enrollments' => 0,
                'admin_panel' => 0,
                'user_roles' => 0,
                'reports' => 1,
            ]
        ];
    }

    /**
     * Team Lead app permissions
     */
    public static function get_permission_team_lead_app()
    {
        return [
            [
                'teams' => 0,
                'members' => 0,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'invoice' => 0,
                'enrollments' => 0,
                'admin_panel' => 0,
                'user_roles' => 0,
                'reports' => 1,
            ]
        ];
    }

    /**
     * Telecaller app permissions
     */
    public static function get_permission_telecaller_app()
    {
        return [
            [
                'teams' => 0,
                'members' => 0,
                'leads' => 1,
                'follow_ups' => 1,
                'call' => 1,
                'candidate' => 1,
                'staff' => 0,
                'invoice' => 0,
                'enrollments' => 0,
                'admin_panel' => 0,
                'user_roles' => 0,
                'reports' => 0,
            ]
        ];
    }

    /**
     * Check if user can access a specific menu item
     */
    public static function can_access_menu($menu_permission)
    {
        return self::has_permission($menu_permission);
    }
}
