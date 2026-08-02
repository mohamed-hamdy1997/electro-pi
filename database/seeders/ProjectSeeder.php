<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Project::factory()->active()->create([
                'user_id' => $user->id,
                'name'    => 'Active Project',
            ]);

            Project::factory()->completed()->create([
                'user_id' => $user->id,
                'name'    => 'Completed Project',
            ]);

            Project::factory()->archived()->create([
                'user_id' => $user->id,
                'name'    => 'Archived Project',
            ]);

            Project::factory(2)->active()->create(['user_id' => $user->id]);
        });
    }
}
