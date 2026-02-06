<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class UpdateEventStatus extends Command
{
    protected $signature = 'update:event-status';

    protected $description = 'Update events status automatically based on dates';

    public function handle()
    {
        Event::whereDate('end_date', '<', now())
            ->update(['status' => 'closed']);

        Event::whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->update(['status' => 'live']);

        Event::whereDate('start_date', '>', now())
            ->update(['status' => 'draft']);

        $this->info('Event statuses updated successfully.');

        return 0;
    }
}
