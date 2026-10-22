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
        Schema::create('asesmen_soal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asesmen_id')->constrained('asesmen')->cascadeOnDelete();
            $table->text('pertanyaan');
            $table->string('tipe_jawaban');
            $table->json('opsi')->nullable();
            $table->foreignUuid('skill_id')->nullable()->constrained('skills')->nullOnDelete();
            $table->foreignUuid('kategori_minat_id')->nullable()->constrained('kategori_minat')->nullOnDelete();
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
        Schema::dropIfExists('asesmen_soal');
    }
};
