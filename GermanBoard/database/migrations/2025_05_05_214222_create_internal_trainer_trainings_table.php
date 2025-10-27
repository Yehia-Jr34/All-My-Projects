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
        Schema::create('internal_trainer_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->foreignId('internal_trainer_id')->references('id')->on('internal_trainers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_trainer_trainings');
    }
};
