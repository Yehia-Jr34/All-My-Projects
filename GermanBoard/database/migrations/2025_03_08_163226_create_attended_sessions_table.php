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
        Schema::create('attended_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->references('id')->on('training_sessions')->onDelete('cascade');
            $table->foreignId('training_trainee_id')->references('id')->on('training_trainees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attended_sessions');
    }
};
