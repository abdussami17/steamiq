<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use Illuminate\Http\Request;
use App\Models\Event;


class DashboardController extends Controller
{
    public function index()
    {
        $studentsCount = \App\Models\Student::count();
        $teamsCount = \App\Models\Team::count();
        $activeEventsCount = \App\Models\Event::where('status', 'live')->count();
        $orgCount = \App\Models\Organization::count();
        $allevents = Event::select('id','name')->get();
    
        $recentActivities = \App\Models\Activity::with('user')
            ->latest() 
            ->get();
    
        return view('dashboard.index', compact(
            'studentsCount',
            'teamsCount',
            'activeEventsCount',
            'allevents',
            'recentActivities',
            'orgCount'
        ));
    }
}
