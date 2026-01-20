<?php

namespace App\Console;

use App\Console\Commands\RunComleamQueue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        RunComleamQueue::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Cron schedule can be configured in cPanel.
    }
}
