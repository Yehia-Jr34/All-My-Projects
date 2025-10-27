<?php

use App\Enum\ArticlesStatusEnum;
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
        Schema::table('global_articles', function (Blueprint $table) {
            //
            $table->enum('status',[
                ArticlesStatusEnum::PENDING->value,
                ArticlesStatusEnum::REJECTED->value,
                ArticlesStatusEnum::APPROVED->value
            ])
                ->default(ArticlesStatusEnum::PENDING)
                ->after('content');
            $table->string('reject_reason')->nullable()->after('content');
            $table->dropColumn('image');
            $table->text('content')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_articles', function (Blueprint $table) {
            // Reverse the additions (drop the new columns)
            $table->dropColumn('status');
            $table->dropColumn('reject_reason');
            $table->string('content')->change();
            // Reverse the deletion (re-add the image column)
            $table->string('image')->nullable(); // Adjust the column definition to match your original
        });
    }
};
