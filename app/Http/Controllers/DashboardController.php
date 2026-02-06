<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $playersCount = \App\Models\Player::count() ?? 0;
        $teamsCount = \App\Models\Team::count() ?? 0;
        $activeEventsCount = \App\Models\Event::where('status', 'live')->count() ?? 0;
        $recentActivities = \App\Models\Activity::latest()->take(5)->get() ?? collect();
    
        return view('dashboard.index', compact(
            'playersCount',
            'teamsCount',
            'activeEventsCount',
            'recentActivities'
        ));
    }
    
}
