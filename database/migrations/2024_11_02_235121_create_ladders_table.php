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
        Schema::create('ladders', function (Blueprint $table) {
            $table->id();
            $table->timestamp('end_at')->nullable();
            $table->foreignId('portfolio_id')
                ->references('id')
                ->on('portfolios')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ladders');
    }
};
