<?php

use App\Models\PaymentRequest;
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
            $table->unsignedBigInteger('amount')->default(0);
            $table->foreignId('user_id')
                ->references('id')->on('users')->cascadeOnDelete();
            $table->string('type')->default(PaymentRequest::WITHDRAW_TYPE);
            $table->string('status')->default(PaymentRequest::CREATED_STATUS);
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
