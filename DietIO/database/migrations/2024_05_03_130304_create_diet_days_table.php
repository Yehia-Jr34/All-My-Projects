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
        Schema::create('diet_days', function (Blueprint $table) {
            $table->id();
            $table->integer('day_number');
            $table->integer('week_number');
            $table->foreignId('meal_id')->references('id')->on('all_meals');
            $table->foreignId('diet_id')->references('id')->on('diets');
            $table->foreignId('meal_type_id')->references('id')->on('type_of_meals');
            $table->string('status')->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_days');
    }
};
