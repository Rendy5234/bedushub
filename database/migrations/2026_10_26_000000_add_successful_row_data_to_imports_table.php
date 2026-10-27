<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan snapshot data per-baris institusi yang berhasil diimpor (maks 10 terakhir)
 * agar modal "Hasil Import" dapat menampikan Nama, Jenis, Kecamatan untuk baris berhasil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->json('successful_row_data')->nullable()->after('successful_rows');
        });
    }

    public function down(): void
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('successful_row_data');
        });
    }
};
