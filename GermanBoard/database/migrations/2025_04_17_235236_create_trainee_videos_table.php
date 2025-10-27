<?php

use App\Enum\VideoStatusEnum;
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
        Schema::create('trainee_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->references('id')->on('trainees')->onDelete('cascade');
            $table->foreignId('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->enum('status', ['locked', 'not watched', 'completed', 'in progress'])->default(VideoStatusEnum::NOT_WATCHED->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainee_videos');
    }
};
