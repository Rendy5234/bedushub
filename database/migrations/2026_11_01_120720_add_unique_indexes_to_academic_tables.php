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
        Schema::table('institusi', function (Blueprint $table) {
            $table->unique('nama');
        });

        Schema::table('fakultas', function (Blueprint $table) {
            $table->unique(['institusi_id', 'nama']);
        });

        Schema::table('program_studi', function (Blueprint $table) {
            $table->unique(['fakultas_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institusi', function (Blueprint $table) {
            $table->dropUnique('institusi_nama_unique');
        });

        Schema::table('fakultas', function (Blueprint $table) {
            $table->dropUnique('fakultas_institusi_id_nama_unique');
        });

        Schema::table('program_studi', function (Blueprint $table) {
            $table->dropUnique('program_studi_fakultas_id_nama_unique');
        });
    }
};
