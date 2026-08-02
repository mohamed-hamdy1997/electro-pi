<?php

namespace App\Services;

use App\Enums\ProjectStatusEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;

class DashboardService
{
    public function getStats(User $user): array
    {
        $projectIds = $user->projects()->pluck('id');

        $tasksQuery = fn () => Task::whereIn('project_id', $projectIds);

        $pendingStatuses = [TaskStatusEnum::TODO->value, TaskStatusEnum::IN_PROGRESS->value];

        return [
            'total_projects'  => $projectIds->count(),
            'active_projects' => $user->projects()->where('status', ProjectStatusEnum::ACTIVE)->count(),
            'total_tasks'     => $tasksQuery()->count(),
            'completed_tasks' => $tasksQuery()->where('status', TaskStatusEnum::DONE)->count(),
            'pending_tasks'   => $tasksQuery()->whereIn('status', $pendingStatuses)->count(),
            'overdue_tasks'   => $tasksQuery()->whereIn('status', $pendingStatuses)
                                              ->whereNotNull('due_date')
                                              ->whereDate('due_date', '<', today())
                                              ->count(),
        ];
    }
}
