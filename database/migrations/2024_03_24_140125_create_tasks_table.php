<?php

use App\Enums\StatusEnum;
use App\Enums\TaskPhaseEnum;
use App\Models\TaskPhase;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_group_id')->nullable()->constrained('task_groups');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('breifing');
            $table->string('details')->nullable();
            $table->dateTime('reminder')->nullable();
            $table->foreignId('phase')->constrained('task_phases')->onDelete('cascade');
            $table->foreignId('type')->constrained('task_types')->onDelete('cascade');
            $table->foreignId('status_id')->constrained('statuses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
