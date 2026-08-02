<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mohamed@example.com'],
            [
                'name'     => 'Mohamed Hamdy',
                'password' => Hash::make('password'),
            ]
        );

        User::factory(4)->create();
    }
}
