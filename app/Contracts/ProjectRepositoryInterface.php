<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface extends RepositoryInterface
{
    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator;
}
