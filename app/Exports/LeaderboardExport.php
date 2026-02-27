<?php 

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Services\LeaderboardService;

class LeaderboardExport implements FromView
{
    public function __construct(private $eventId) {}

    public function view(): View
    {
        [$rows, $categories] = app(LeaderboardService::class)->build($this->eventId);

        return view('leaderboard.export', compact('rows','categories'));
    }
}