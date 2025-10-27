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
        Schema::create('registeration_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->references('id')->on('admins');
            $table->foreignId('doctor_id')->references('id')->on('doctors');
            $table->string('status')->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registeration_requests');
    }
};
