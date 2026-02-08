<?php

namespace App\Exports;

use App\Models\Matches;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ScheduleExport implements FromView
{
    public function view(): View
    {
        // Fetch all matches with related teams and event
        $matches = Matches::with(['teamA', 'teamB', 'event'])
            ->orderBy('event_id')
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('schedule.export', [
            'matches' => $matches
        ]);
    }
}
