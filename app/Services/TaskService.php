<?php

namespace App\Services;

use App\Contracts\TaskRepositoryInterface;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository
    ) {}

    public function index(Project $project, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->paginateForProject($project->id, $filters);
    }

    public function store(Project $project, array $data): Task
    {
        return $this->taskRepository->create(array_merge($data, ['project_id' => $project->id]));
    }

    public function update(Task $task, array $data): Task
    {
        return $this->taskRepository->update($task, $data);
    }

    public function destroy(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }
}
