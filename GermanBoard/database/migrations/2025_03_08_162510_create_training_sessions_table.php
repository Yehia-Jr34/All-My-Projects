<?php

use App\Enum\SessionStatusEnum;
use App\Enum\TrainingSessionDetailsEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->dateTime('start_date');
            $table->text('title');
            $table->text('notes')->nullable();
            $table->string('status')->default(SessionStatusEnum::NOT_STARTED->value);
            $table->timestamps();
        });

        Schema::create('training_sessions_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->references('id')->on('training_sessions')->onDelete('cascade');
            $table->string('name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status')->default(TrainingSessionDetailsEnum::STARTED->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
