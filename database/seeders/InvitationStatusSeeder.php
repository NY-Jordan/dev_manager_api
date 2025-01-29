<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvitationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\InvitationStatus::factory()->create([
            'name' => 'cancel'
         ]);

        \App\Models\InvitationStatus::factory()->create([
            'name' => 'accepted'
         ]);

         \App\Models\InvitationStatus::factory()->create([
            'name' => 'refused'
         ]);
         \App\Models\InvitationStatus::factory()->create([
            'name' => 'pending'
         ]);
    }
}
