<?php

namespace App\Jobs;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyOverdueTasksJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Task::with('project.user')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', [TaskStatusEnum::DONE])
            ->each(function (Task $task) {
                $task->project->user->notify(new TaskOverdueNotification($task));
            });
    }
}
