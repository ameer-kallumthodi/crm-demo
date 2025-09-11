<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\LeadStatus;
use App\Models\Country;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $data = [
            'leadStatuses' => Lead::statusWithCount(),
            'topTelecallers' => $this->getTopTelecallers(),
            'topCountries' => $this->getTopCountries(),
            'totalLeads' => Lead::count(),
            'totalUsers' => User::count(),
        ];

        return view('dashboard', $data);
    }

    /**
     * Get top telecallers by lead count.
     */
    private function getTopTelecallers()
    {
        // Optimized query to get telecallers with lead counts in a single query
        $telecallers = User::select('users.id', 'users.name', 'users.phone', 'users.profile_picture')
            ->selectRaw('COUNT(leads.id) as lead_count')
            ->leftJoin('leads', 'users.id', '=', 'leads.telecaller_id')
            ->where('users.role_id', 6)
            ->whereNull('users.deleted_at')
            ->whereNull('leads.deleted_at')
            ->groupBy('users.id', 'users.name', 'users.phone', 'users.profile_picture')
            ->having('lead_count', '>', 0)
            ->orderByDesc('lead_count')
            ->limit(5)
            ->get()
            ->map(function ($telecaller) {
                return [
                    'id' => $telecaller->id,
                    'name' => $telecaller->name,
                    'phone' => $telecaller->phone,
                    'profile_picture' => $telecaller->profile_picture,
                    'count' => $telecaller->lead_count,
                ];
            });

        return $telecallers;
    }

    /**
     * Get top countries by lead count.
     */
    private function getTopCountries()
    {
        // Optimized query to get countries with lead counts in a single query
        $countries = Country::select('countries.id', 'countries.title')
            ->selectRaw('COUNT(leads.id) as lead_count')
            ->leftJoin('leads', 'countries.id', '=', 'leads.country_id')
            ->whereNull('countries.deleted_at')
            ->whereNull('leads.deleted_at')
            ->groupBy('countries.id', 'countries.title')
            ->having('lead_count', '>', 0)
            ->orderByDesc('lead_count')
            ->limit(5)
            ->get()
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'title' => $country->title,
                    'count' => $country->lead_count,
                ];
            });

        return $countries;
    }
}
