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
        Schema::create('internal_trainer_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_trainer_id')->nullable()->references('id')->on('internal_trainers');
            $table->foreignId('training_id')->nullable()->references('id')->on('trainings');
            $table->string('action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_trainer_actions');
    }
};
