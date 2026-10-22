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
        Schema::create('potensi_daerah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sektor_id')->constrained('sektor_unggulan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('lokasi_kecamatan')->nullable()->index();
            $table->text('deskripsi')->nullable();
            $table->text('peluang_usaha')->nullable();
            $table->text('kebutuhan_skill')->nullable();
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
        Schema::dropIfExists('potensi_daerah');
    }
};
