<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_invitaions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->integer('status')->default(1); // 1 pending, 2 accepted, 3 refused
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_invitaions');
    }
};
