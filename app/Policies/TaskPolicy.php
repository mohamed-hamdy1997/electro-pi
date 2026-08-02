<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $this->sameOwner($user, $task);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->sameOwner($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->sameOwner($user, $task);
    }

    private function sameOwner(User $user, Task $task): bool
    {
        return $user->id === $task->project->user_id;
    }
}
