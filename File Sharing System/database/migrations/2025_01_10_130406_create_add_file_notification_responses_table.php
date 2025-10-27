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
        Schema::create('add_file_notification_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->references('id')->on('files');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('group_id')->references('id')->on('groups');
            $table->text('notification_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_file_notification_responses');
    }
};
