<?php

namespace Database\Seeders;

use App\Enums\TaskPhaseEnum;
use App\Enums\TaskStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\TaskPhase::factory()->create([
            'name' => TaskPhaseEnum::BACKLOG
         ]);
         \App\Models\TaskPhase::factory()->create([
            'name' => TaskPhaseEnum::STARTED
         ]);
         \App\Models\TaskPhase::factory()->create([
            'name' => TaskPhaseEnum::IN_PROGRESS
         ]);
         \App\Models\TaskPhase::factory()->create([
            'name' => TaskPhaseEnum::DONE
         ]);
    }
}
