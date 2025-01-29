<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Status::factory()->create([
            'name' => 'inactive'
         ]);

        \App\Models\Status::factory()->create([
            'name' => 'active'
         ]);

         \App\Models\Status::factory()->create([
            'name' => 'delete'
         ]);
    }
}
