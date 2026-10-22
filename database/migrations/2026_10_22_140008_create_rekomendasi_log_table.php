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
        Schema::create('rekomendasi_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete()->index();
            $table->string('tipe_item')->index();
            $table->uuid('item_id')->index();
            $table->decimal('skor', 5, 4)->nullable();
            $table->text('alasan')->nullable();
            $table->string('aksi')->nullable()->index();
            $table->string('model_version')->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index komposit untuk query ML
            $table->index(['user_id', 'tipe_item', 'item_id']);
            $table->index(['user_id', 'model_version', 'event_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_log');
    }
};
