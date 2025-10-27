<?php

use App\Enum\PaymentRequestStatusEnum;
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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->references('id')->on('providers');
            $table->string('account_number');
            $table->float('amount');
            $table->enum('status' , [
                PaymentRequestStatusEnum::PENDING->value,
                PaymentRequestStatusEnum::APPROVED->value,
                ])->default(PaymentRequestStatusEnum::PENDING->value);
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
