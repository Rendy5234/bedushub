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
        Schema::create('user_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete()->index();
            $table->string('item_type')->index();
            $table->uuid('item_id')->index();
            $table->string('label')->nullable();
            $table->integer('rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('event_at')->index();
            $table->timestamps();

            // Index komposit untuk query ML
            $table->index(['user_id', 'item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feedback');
    }
};
