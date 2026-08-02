<?php

namespace Tests\Feature\Projects;

use App\Enums\ProjectStatusEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user  = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}");

        return $user;
    }

    public function test_unauthenticated_user_cannot_access_projects(): void
    {
        $this->getJson('/api/v1/projects')->assertStatus(401);
    }

    public function test_user_can_list_own_projects(): void
    {
        $user = $this->actingAsUser();

        Project::factory()->count(3)->create(['user_id' => $user->id]);
        Project::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_projects_list_is_paginated(): void
    {
        $user = $this->actingAsUser();

        Project::factory()->count(15)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'meta', 'links']);
    }

    public function test_user_can_create_project(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/projects', [
            'name'        => 'New Project',
            'description' => 'A test project',
            'status'      => ProjectStatusEnum::ACTIVE->value,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'project' => ['id', 'name', 'description', 'status', 'created_at'],
                 ]);

        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    public function test_create_project_fails_without_name(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/projects', [
            'description' => 'Missing name',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    public function test_create_project_fails_with_invalid_status(): void
    {
        $this->actingAsUser();

        $response = $this->postJson('/api/v1/projects', [
            'name'   => 'Project',
            'status' => 'invalid-status',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['status']);
    }

    public function test_user_can_view_own_project(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/v1/projects/{$project->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $project->id, 'name' => $project->name]);
    }

    public function test_user_cannot_view_another_users_project(): void
    {
        $this->actingAsUser();
        $otherProject = Project::factory()->create();

        $this->getJson("/api/v1/projects/{$otherProject->id}")
             ->assertStatus(403);
    }

    public function test_user_can_update_own_project(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/v1/projects/{$project->id}", [
            'name'   => 'Updated Name',
            'status' => ProjectStatusEnum::COMPLETED->value,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Name', 'status' => ProjectStatusEnum::COMPLETED->value]);
    }

    public function test_user_cannot_update_another_users_project(): void
    {
        $this->actingAsUser();
        $otherProject = Project::factory()->create();

        $this->putJson("/api/v1/projects/{$otherProject->id}", ['name' => 'Hacked'])
             ->assertStatus(403);
    }

    public function test_user_can_soft_delete_own_project(): void
    {
        $user    = $this->actingAsUser();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->deleteJson("/api/v1/projects/{$project->id}")
             ->assertStatus(200)
             ->assertJson(['message' => 'Project deleted successfully.']);

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_user_cannot_delete_another_users_project(): void
    {
        $this->actingAsUser();
        $otherProject = Project::factory()->create();

        $this->deleteJson("/api/v1/projects/{$otherProject->id}")
             ->assertStatus(403);
    }
}
