<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(1)->create();

         \App\Models\Project::factory(2)->create();

          $this->call([
            TaskStatusSeeder::class,
            TaskTypeSeeder::class
          ]);

    }
}
