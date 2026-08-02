<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Project::all()->each(function (Project $project) {
            Task::factory(3)->todo()->create(['project_id' => $project->id]);

            Task::factory(2)->inProgress()->create(['project_id' => $project->id]);

            Task::factory(2)->done()->create(['project_id' => $project->id]);

            Task::factory(2)->overdue()->create(['project_id' => $project->id]);
        });
    }
}
