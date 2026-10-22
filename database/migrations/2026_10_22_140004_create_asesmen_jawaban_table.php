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
        Schema::create('asesmen_jawaban', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asesmen_id')->constrained('asesmen')->cascadeOnDelete();
            $table->foreignUuid('soal_id')->constrained('asesmen_soal')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('jawaban')->nullable();
            $table->integer('skor')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Audit trail (child table with timestamps & softDeletes).
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesmen_jawaban');
    }
};
