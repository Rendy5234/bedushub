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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('domisili_kecamatan')->nullable()->index();
            $table->string('jenjang')->nullable()->index();
            $table->foreignUuid('institusi_id')->nullable()->constrained('institusi')->nullOnDelete();
            $table->foreignUuid('fakultas_id')->nullable()->constrained('fakultas')->nullOnDelete();
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->foreignUuid('prodi_id')->nullable()->constrained('program_studi')->nullOnDelete();
            $table->integer('tahun_lulus')->nullable();
            $table->string('status')->default('aktif')->index();
            $table->string('no_telp')->nullable();
            $table->string('foto')->nullable();
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
        Schema::dropIfExists('profiles');
    }
};
