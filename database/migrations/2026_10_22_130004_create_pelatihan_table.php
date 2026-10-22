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
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('topik')->nullable();
            $table->string('level')->nullable()->index();
            $table->string('instruktur')->nullable();
            $table->string('jenis')->nullable()->index();
            $table->date('tanggal_mulai')->nullable()->index();
            $table->date('tanggal_selesai')->nullable()->index();
            $table->string('jam_mulai')->nullable();
            $table->string('jam_selesai')->nullable();
            $table->string('tempat')->nullable();
            $table->integer('kuota')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('thumbnail')->nullable();
            $table->string('link_materi')->nullable();
            $table->string('link_sertifikat')->nullable();
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
        Schema::dropIfExists('pelatihan');
    }
};
