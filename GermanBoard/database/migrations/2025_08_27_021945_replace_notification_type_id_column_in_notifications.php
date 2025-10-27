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
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['notification_type_id']);

            $table->dropColumn('notification_type_id');
            $table->string('notification_type'); // NotificationTypeEnum
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // First, remove the new column
            $table->dropColumn('notification_type');

            $table->foreignId('notification_type_id')->references('id')->on('notification_types')->onDelete('cascade');

        });
    }
};
