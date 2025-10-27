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
        Schema::create('training_trainees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->references('id')->on('trainees');
            $table->foreignId('training_id')->references('id')->on('trainings');
            $table->boolean('passed_the_training')->default(false);
            $table->decimal('remaining_hours', 2)->nullable();
            $table->decimal('achievement_percentage')->nullable();
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_trainees');
    }
};
