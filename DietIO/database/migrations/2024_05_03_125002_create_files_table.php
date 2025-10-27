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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('weight');
            $table->integer('height');
            $table->integer('age');
            $table->string('gender');
            $table->double('waistline');
            $table->double('buttocks_cir');
            $table->foreignId('target')->references('id')->on('tags');
            $table->integer('number_of_meals');
            $table->foreignId('activity_id')->references('id')->on('activities');
            $table->foreignId('type_of_diet_id')->references('id')->on('type_of_diets');
            $table->text('diseases');
            $table->text('surgery');
            $table->time('wake_up');
            $table->time('sleep');
            $table->string('job');
            $table->string('study');
            $table->text('daily_routine');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
