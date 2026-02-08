<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $playersCount = \App\Models\Player::count();
        $teamsCount = \App\Models\Team::count();
        $activeEventsCount = \App\Models\Event::where('status', 'live')->count();
        $recentActivities = \App\Models\Activity::latest()->take(5)->get();
    
        $todayMatchesCount = Matches::whereDate('date', Carbon::today())->count();
    
        return view('dashboard.index', compact(
            'playersCount',
            'teamsCount',
            'activeEventsCount',
            'recentActivities',
            'todayMatchesCount'
        ));
    }
    
}
