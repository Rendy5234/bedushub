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
        Schema::create('lowongan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('perusahaan_id')->constrained('perusahaan')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->text('kualifikasi')->nullable();
            $table->string('tipe_pekerjaan')->nullable()->index();
            $table->string('lokasi')->nullable()->index();
            $table->integer('gaji_min')->nullable();
            $table->integer('gaji_max')->nullable();
            $table->date('tanggal_mulai')->nullable()->index();
            $table->date('tanggal_berakhir')->nullable()->index();
            $table->string('status')->default('aktif')->index();
            $table->integer('kuota')->nullable();
            $table->integer('kuota_diterima')->nullable();
            $table->string('pendidikan')->nullable()->index();
            $table->text('benefit')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('butuh_surat_lamaran')->default(false);
            $table->boolean('butuh_pas_foto')->default(false);
            $table->string('rasio_pas_foto')->nullable();
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
        Schema::dropIfExists('lowongan');
    }
};
