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
            $table->foreignId('taskgroup_id')->constrained('task_groups')->onDelete('cascade');
            $table->string('title');
            $table->string('breifing');
            $table->string('details');
            $table->dateTime('reminder')->nullable();
            $table->foreignId('phase')->constrained('task_phases')->onDelete('cascade');
            $table->string('status')->default(StatusEnum::STATUS_ACTIVE);
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
