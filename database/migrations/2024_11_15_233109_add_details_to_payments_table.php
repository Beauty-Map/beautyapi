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
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('coins');
            $table->string('status')->default(\App\Models\Payment::CREATED);
            $table->string('code');
            $table->timestamp('expire_at')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('payment_option_id')
                ->nullable()
                ->references('id')->on('payment_options')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('coins');
            $table->dropColumn('status');
            $table->dropColumn('code');
            $table->dropColumn('expire_at');
            $table->dropColumn('user_id');
            $table->dropColumn('payment_option_id');
        });
    }
};
