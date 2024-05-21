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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status')->default('created');
            $table->longText('description');
            $table->string('file')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreignId('subject_id')
                ->references('id')
                ->on('ticket_subjects')
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('tickets')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
