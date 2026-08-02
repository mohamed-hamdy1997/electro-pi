<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface extends RepositoryInterface
{
    public function paginateForProject(int $projectId, array $filters = [], int $perPage = 10): LengthAwarePaginator;
}
