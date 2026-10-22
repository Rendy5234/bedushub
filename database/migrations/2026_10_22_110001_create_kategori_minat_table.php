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
        Schema::create('kategori_minat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
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
        Schema::dropIfExists('kategori_minat');
    }
};
