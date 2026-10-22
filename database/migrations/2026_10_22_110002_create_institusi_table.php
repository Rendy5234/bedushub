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
        Schema::create('institusi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('jenis');
            $table->text('alamat')->nullable();
            $table->string('kecamatan')->nullable()->index();
            $table->string('status')->default('aktif')->index();
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
        Schema::dropIfExists('institusi');
    }
};
