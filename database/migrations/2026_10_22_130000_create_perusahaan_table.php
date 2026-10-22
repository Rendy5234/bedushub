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
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama');
            $table->string('bidang_usaha')->nullable()->index();
            $table->text('alamat')->nullable();
            $table->string('kecamatan')->nullable()->index();
            $table->string('no_telp')->nullable();
            $table->string('logo')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status_kerjasama')->default('aktif')->index();
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
        Schema::dropIfExists('perusahaan');
    }
};
