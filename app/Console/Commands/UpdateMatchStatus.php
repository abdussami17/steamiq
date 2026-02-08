<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Matches;
use Carbon\Carbon;

class UpdateMatchStatus extends Command
{
    protected $signature = 'matches:update-status';
    protected $description = 'Update match status based on scheduled date & time';

    public function handle()
    {
        $now = Carbon::now();

        // Scheduled matches check karo
        $matches = Matches::where('status', 'scheduled')->get();

        foreach ($matches as $match) {
            $matchDateTime = Carbon::parse($match->date . ' ' . $match->time);

            if ($now->greaterThanOrEqualTo($matchDateTime)) {
                $match->status = 'live';
                $match->save();
                $this->info("Match ID {$match->id} is now Live!");
            }
        }
    }
}
