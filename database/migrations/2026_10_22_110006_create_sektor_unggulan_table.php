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
        Schema::create('sektor_unggulan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable()->index();
            $table->string('kecamatan')->nullable()->index();
            $table->string('ikon')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Audit trail.
            $table->foreignUuid('created_by')->nullable();
            $table->foreignUuid('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sektor_unggulan');
    }
};
