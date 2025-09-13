<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Get lead status color based on status ID
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusColor($statusId)
    {
        switch ($statusId) {
            case 1: // Un Touched Leads
                return 'danger';
            case 2: // Follow-up
                return 'warning';
            case 3: // Not-interested IN FULL COURSE
                return 'danger';
            case 4: // Disqualified
                return 'danger';
            case 5: // DNP
                return 'danger';
            case 6: // Demo
                return 'info';
            case 7: // Interested to Buy
                return 'success';
            case 8: // Positive
                return 'success';
            case 9: // May Buy Later
                return 'warning';
            default:
                return 'secondary';
        }
    }

    /**
     * Get lead status color class for Bootstrap
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusColorClass($statusId)
    {
        $color = self::getLeadStatusColor($statusId);
        return "bg-light-{$color} text-{$color}";
    }

    /**
     * Get lead status badge class for Bootstrap
     * 
     * @param int $statusId
     * @return string
     */
    public static function getLeadStatusBadgeClass($statusId)
    {
        $color = self::getLeadStatusColor($statusId);
        return "badge bg-{$color}";
    }
}
