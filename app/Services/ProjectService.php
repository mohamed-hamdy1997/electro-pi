<?php

namespace App\Services;

use App\Contracts\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository
    ) {}

    public function index(User $user): LengthAwarePaginator
    {
        return $this->projectRepository->paginateForUser($user->id);
    }

    public function store(User $user, array $data): Project
    {
        $project = $this->projectRepository->create(array_merge($data, ['user_id' => $user->id]));

        return $project->loadCount('tasks');
    }

    public function update(Project $project, array $data): Project
    {
        return $this->projectRepository->update($project, $data)->loadCount('tasks');
    }

    public function destroy(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }
}
