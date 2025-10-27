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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->string('title');
            $table->string('passing_score');
            $table->text('description');
            $table->foreignId('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->string('question_text');
            $table->timestamps();
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->string('answer_text');
            $table->boolean('is_correct');
            $table->timestamps();
        });

        /*
        Schema::create('training_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->foreignId('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreignId('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('recorded_training_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->foreignId('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreignId('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->string('quiz_score');
            $table->dateTime('completed_at');
            $table->foreignId('trainee_id')->references('id')->on('trainees');
            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
