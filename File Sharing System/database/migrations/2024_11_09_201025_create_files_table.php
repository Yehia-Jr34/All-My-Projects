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
            $table->foreignId('group_id')->constrained('groups');
            $table->foreignId('created_by')->constrained('users');
            $table->string('name');
            $table->boolean('status');
            $table->enum('admin_group_approve' , ['accepted' , 'rejected' ,'pending' ])->default('pending');
            $table->dateTime('approved_at')->nullable();
            $table->double('size');
            $table->index('approved_at');
            $table->index('admin_group_approve');

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
