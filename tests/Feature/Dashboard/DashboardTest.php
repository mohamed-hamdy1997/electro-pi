<?php

namespace Tests\Feature\Dashboard;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user  = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}");

        return $user;
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $this->getJson('/api/v1/dashboard')->assertStatus(401);
    }

    public function test_dashboard_returns_correct_structure(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/v1/dashboard')
             ->assertStatus(200)
             ->assertJsonStructure([
                 'total_projects',
                 'active_projects',
                 'total_tasks',
                 'completed_tasks',
                 'pending_tasks',
                 'overdue_tasks',
             ]);
    }

    public function test_dashboard_returns_zeros_when_no_data(): void
    {
        $this->actingAsUser();

        $this->getJson('/api/v1/dashboard')
             ->assertStatus(200)
             ->assertExactJson([
                 'total_projects'  => 0,
                 'active_projects' => 0,
                 'total_tasks'     => 0,
                 'completed_tasks' => 0,
                 'pending_tasks'   => 0,
                 'overdue_tasks'   => 0,
             ]);
    }

    public function test_dashboard_counts_only_own_projects(): void
    {
        $user = $this->actingAsUser();

        Project::factory()->count(3)->create(['user_id' => $user->id]);
        Project::factory()->count(2)->create();

        $this->getJson('/api/v1/dashboard')
             ->assertStatus(200)
             ->assertJsonFragment(['total_projects' => 3]);
    }

    public function test_dashboard_counts_active_projects(): void
    {
        $user = $this->actingAsUser();

        Project::factory()->count(2)->active()->create(['user_id' => $user->id]);
        Project::factory()->count(1)->completed()->create(['user_id' => $user->id]);
        Project::factory()->count(1)->archived()->create(['user_id' => $user->id]);

        $this->getJson('/api/v1/dashboard')
             ->assertJsonFragment([
                 'total_projects'  => 4,
                 'active_projects' => 2,
             ]);
    }

    public function test_dashboard_counts_completed_and_pending_tasks(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->count(2)->done()->create(['project_id' => $project->id]);
        Task::factory()->count(3)->todo()->create(['project_id' => $project->id]);
        Task::factory()->count(1)->inProgress()->create(['project_id' => $project->id]);

        $this->getJson('/api/v1/dashboard')
             ->assertJsonFragment([
                 'total_tasks'     => 6,
                 'completed_tasks' => 2,
                 'pending_tasks'   => 4,
             ]);
    }

    public function test_dashboard_counts_overdue_tasks(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->count(2)->overdue()->create(['project_id' => $project->id]);
        Task::factory()->count(1)->done()->create([
            'project_id' => $project->id,
            'due_date'   => now()->subDay()->toDateString(),
        ]);

        $this->getJson('/api/v1/dashboard')
             ->assertJsonFragment(['overdue_tasks' => 2]);
    }

    public function test_dashboard_does_not_count_other_users_tasks(): void
    {
        $user         = $this->actingAsUser();
        $project      = Project::factory()->create(['user_id' => $user->id]);
        $otherProject = Project::factory()->create();

        Task::factory()->count(2)->create(['project_id' => $project->id]);
        Task::factory()->count(5)->create(['project_id' => $otherProject->id]);

        $this->getJson('/api/v1/dashboard')
             ->assertJsonFragment(['total_tasks' => 2]);
    }
}
