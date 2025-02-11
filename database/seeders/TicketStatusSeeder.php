<?php

namespace Database\Seeders;

use App\Enums\TicketStatusEnum;
use App\Enums\TicketTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\TicketStatus::factory()->create([
            'name' => TicketStatusEnum::IN_PROGRESS
         ]);
         \App\Models\TicketStatus::factory()->create([
            'name' => TicketStatusEnum::DONE
         ]);
    }
}
