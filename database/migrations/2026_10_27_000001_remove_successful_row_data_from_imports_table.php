<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot data baris berhasil tidak lagi dipakai. Modal "Hasil Import" hanya
 * menampilkan statistik angka, sehingga kolom JSON ini dihapus agar database
 * tidak membengkak.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imports', function (Blueprint $table): void {
            $table->dropColumn('successful_row_data');
        });
    }

    public function down(): void
    {
        Schema::table('imports', function (Blueprint $table): void {
            $table->json('successful_row_data')->nullable()->after('successful_rows');
        });
    }
};
