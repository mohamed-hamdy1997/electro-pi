<?php

namespace App\Repositories;

use App\Contracts\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Task());
    }

    public function paginateForProject(int $projectId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->where('project_id', $projectId);

        if (data_get($filters, 'status')) {
            $query->where('status', data_get($filters, 'status'));
        }

        if (data_get($filters, 'priority')) {
            $query->where('priority', data_get($filters, 'priority'));
        }

        if (data_get($filters, 'search')) {
            $query->where('title', 'like', '%' . data_get($filters, 'search') . '%');
        }

        return $query->latest()->paginate($perPage);
    }
}
