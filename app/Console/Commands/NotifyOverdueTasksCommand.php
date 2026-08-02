<?php

namespace App\Console\Commands;

use App\Jobs\NotifyOverdueTasksJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tasks:notify-overdue')]
#[Description('Dispatch job to notify project owners about overdue tasks')]
class NotifyOverdueTasksCommand extends Command
{
    public function handle(): void
    {
        NotifyOverdueTasksJob::dispatch();

        $this->info('Overdue tasks notification job dispatched successfully.');
    }
}
