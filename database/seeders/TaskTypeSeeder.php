<?php

namespace Database\Seeders;

use App\Enums\TaskTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\TaskType::factory()->create([
            'name' => TaskTypeEnum::ASSIGN
         ]);
         \App\Models\TaskType::factory()->create([
            'name' => TaskTypeEnum::OWN
         ]);
    }
}
