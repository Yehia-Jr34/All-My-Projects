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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('title_du')->nullable();
            $table->text('about_ar');
            $table->text('about_en');
            $table->text('about_du')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_in_hours');
            $table->string('price');
            $table->string('type');
            $table->double('rate')->default(0.0);
            $table->string('language');
            $table->string('training_site')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
