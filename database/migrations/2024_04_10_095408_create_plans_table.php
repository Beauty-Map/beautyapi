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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('coins')->default(0);
            $table->unsignedInteger('portfolio_count')->default(0);
            $table->unsignedInteger('laddering_count')->default(0);
            $table->unsignedInteger('star_count')->default(0);
            $table->unsignedInteger('image_upload_count')->default(0);
            $table->boolean('has_blue_tick')->default(false);
            $table->boolean('has_discount')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
