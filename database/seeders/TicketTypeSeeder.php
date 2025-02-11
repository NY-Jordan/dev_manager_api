<?php

namespace Database\Seeders;

use App\Enums\TicketTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\TicketType::factory()->create([
            'name' => TicketTypeEnum::BUG
         ]);
         \App\Models\TicketType::factory()->create([
            'name' => TicketTypeEnum::IMPROVMENT
         ]);

         \App\Models\TicketType::factory()->create([
            'name' => TicketTypeEnum::STORY
         ]);
         \App\Models\TicketType::factory()->create([
            'name' => TicketTypeEnum::SUB_TASK
         ]);
    }
}
