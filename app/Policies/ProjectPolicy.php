<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $this->sameOwner($user, $project);
    }

    public function update(User $user, Project $project): bool
    {
        return $this->sameOwner($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->sameOwner($user, $project);
    }

    private function sameOwner(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}
