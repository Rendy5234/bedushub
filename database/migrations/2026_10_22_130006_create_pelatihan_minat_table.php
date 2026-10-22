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
        Schema::create('pelatihan_minat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pelatihan_id')->constrained('pelatihan')->cascadeOnDelete();
            $table->foreignUuid('kategori_minat_id')->constrained('kategori_minat')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Audit trail (pivot with timestamps & softDeletes).
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihan_minat');
    }
};
