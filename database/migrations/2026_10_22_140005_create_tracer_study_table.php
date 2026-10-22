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
        Schema::create('tracer_study', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status_pekerjaan')->nullable()->index();
            $table->string('nama_perusahaan')->nullable();
            $table->string('posisi')->nullable();
            $table->string('bidang_pekerjaan')->nullable()->index();
            $table->string('penghasilan')->nullable();
            $table->integer('relevansi_pekerjaan')->nullable();
            $table->integer('kepuasan_pendidikan')->nullable();
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
        Schema::dropIfExists('tracer_study');
    }
};
