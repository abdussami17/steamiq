<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $studentsCount = \App\Models\Student::count();
        $teamsCount = \App\Models\Team::count();
        $activeEventsCount = \App\Models\Event::where('status', 'live')->count();
        $recentActivities = \App\Models\Activity::latest()->take(5)->get();
    $orgCount = \App\Models\Organization::count();
        $todayMatchesCount = Matches::whereDate('date', Carbon::today())->count();
    
        return view('dashboard.index', compact(
            'studentsCount',
            'teamsCount',
            'activeEventsCount',
            'recentActivities',
            'orgCount',
            'todayMatchesCount'
        ));
    }
    
}
