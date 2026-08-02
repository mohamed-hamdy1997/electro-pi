<?php

namespace Tests\Feature\Tasks;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user  = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}");

        return $user;
    }

    private function url(Project $project, ?Task $task = null): string
    {
        $base = "/api/v1/projects/{$project->id}/tasks";

        return $task ? "{$base}/{$task->id}" : $base;
    }

    public function test_unauthenticated_user_cannot_access_tasks(): void
    {
        $project = Project::factory()->create();

        $this->getJson($this->url($project))->assertStatus(401);
    }

    public function test_user_can_list_tasks_for_own_project(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->count(3)->create(['project_id' => $project->id]);

        $this->getJson($this->url($project))
             ->assertStatus(200)
             ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_list_tasks_of_another_users_project(): void
    {
        $this->actingAsUser();
        $otherProject = Project::factory()->create();

        $this->getJson($this->url($otherProject))->assertStatus(403);
    }

    public function test_tasks_list_is_paginated(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->count(15)->create(['project_id' => $project->id]);

        $this->getJson($this->url($project))
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta', 'links']);
    }

    public function test_user_can_filter_tasks_by_status(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->count(2)->todo()->create(['project_id' => $project->id]);
        Task::factory()->count(3)->done()->create(['project_id' => $project->id]);

        $this->getJson($this->url($project) . '?status=' . TaskStatusEnum::TODO->value)
             ->assertStatus(200)
             ->assertJsonCount(2, 'data');
    }

    public function test_user_can_filter_tasks_by_priority(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->count(2)->highPriority()->create(['project_id' => $project->id]);
        Task::factory()->count(3)->create(['project_id' => $project->id, 'priority' => TaskPriorityEnum::LOW]);

        $this->getJson($this->url($project) . '?priority=' . TaskPriorityEnum::HIGH->value)
             ->assertStatus(200)
             ->assertJsonCount(2, 'data');
    }

    public function test_user_can_search_tasks_by_title(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        Task::factory()->create(['project_id' => $project->id, 'title' => 'Fix login bug']);
        Task::factory()->create(['project_id' => $project->id, 'title' => 'Add dashboard']);

        $this->getJson($this->url($project) . '?search=login')
             ->assertStatus(200)
             ->assertJsonCount(1, 'data')
             ->assertJsonFragment(['title' => 'Fix login bug']);
    }

    public function test_user_can_create_task(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson($this->url($project), [
            'title'    => 'New Task',
            'priority' => TaskPriorityEnum::HIGH->value,
            'status'   => TaskStatusEnum::TODO->value,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'task' => ['id', 'title', 'priority', 'status', 'created_at'],
                 ]);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    public function test_create_task_fails_without_title(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->postJson($this->url($project), ['priority' => TaskPriorityEnum::LOW->value])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['title']);
    }

    public function test_user_cannot_create_task_in_another_users_project(): void
    {
        $this->actingAsUser();
        $otherProject = Project::factory()->create();

        $this->postJson($this->url($otherProject), ['title' => 'Hacked task'])
             ->assertStatus(403);
    }

    public function test_user_can_view_own_task(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task    = Task::factory()->create(['project_id' => $project->id]);

        $this->getJson($this->url($project, $task))
             ->assertStatus(200)
             ->assertJsonFragment(['id' => $task->id, 'title' => $task->title]);
    }

    public function test_user_can_update_own_task(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task    = Task::factory()->create(['project_id' => $project->id]);

        $this->putJson($this->url($project, $task), [
            'title'  => 'Updated Title',
            'status' => TaskStatusEnum::DONE->value,
        ])
             ->assertStatus(200)
             ->assertJsonFragment(['title' => 'Updated Title', 'status' => TaskStatusEnum::DONE->value]);
    }

    public function test_user_cannot_update_another_users_task(): void
    {
        $this->actingAsUser();
        $otherProject = Project::factory()->create();
        $otherTask    = Task::factory()->create(['project_id' => $otherProject->id]);

        $this->putJson($this->url($otherProject, $otherTask), ['title' => 'Hacked'])
             ->assertStatus(403);
    }

    public function test_user_can_soft_delete_own_task(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task    = Task::factory()->create(['project_id' => $project->id]);

        $this->deleteJson($this->url($project, $task))
             ->assertStatus(200)
             ->assertJson(['message' => 'Task deleted successfully.']);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $this->actingAsUser();
        $otherProject = Project::factory()->create();
        $otherTask    = Task::factory()->create(['project_id' => $otherProject->id]);

        $this->deleteJson($this->url($otherProject, $otherTask))
             ->assertStatus(403);
    }
}
