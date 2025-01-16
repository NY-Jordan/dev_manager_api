<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
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
            'name' => TaskPhaseEnum::BACKLOG,
            'status_id' => StatusEnum::STATUS_ACTIVE

         ]);
         \App\Models\TaskPhase::factory()->create([
            'name' => TaskPhaseEnum::STARTED,
            'status_id' => StatusEnum::STATUS_ACTIVE

         ]);
         \App\Models\TaskPhase::factory()->create([
            'name' => TaskPhaseEnum::IN_REVIEW,
            'status_id' => StatusEnum::STATUS_ACTIVE

         ]);
         \App\Models\TaskPhase::factory()->create([
            'name' => TaskPhaseEnum::DONE,
            'status_id' => StatusEnum::STATUS_ACTIVE

         ]);
    }
}
