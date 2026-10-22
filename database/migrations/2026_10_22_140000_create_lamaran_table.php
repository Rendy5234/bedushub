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
        Schema::create('lamaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lowongan_id')->constrained('lowongan')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('diproses')->index();
            $table->string('cv')->nullable();
            $table->string('ijazah')->nullable();
            $table->string('surat_lamaran')->nullable();
            $table->string('pas_foto')->nullable();
            $table->json('dokumen_pendukung')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Audit trail.
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lamaran');
    }
};
