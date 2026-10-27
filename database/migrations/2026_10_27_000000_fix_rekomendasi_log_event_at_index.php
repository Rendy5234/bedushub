<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Memperbaiki index komposit `rekomendasi_log` yang dibuat memakai kolom
 * `event_at`. Kolom tersebut tidak pernah ada di tabel ini (hanya `created_at`),
 * sehingga index menjadi tidak valid dan membuat SQLite menolak operasi
 * ALTER TABLE seperti DROP COLUMN pada tabel mana pun.
 */
return new class extends Migration
{
    private const BROKEN_INDEX = 'rekomendasi_log_user_id_model_version_event_at_index';

    private const FIXED_INDEX = 'rekomendasi_log_user_id_model_version_created_at_index';

    public function up(): void
    {
        if (! Schema::hasTable('rekomendasi_log')) {
            return;
        }

        try {
            Schema::table('rekomendasi_log', function (Blueprint $table): void {
                $table->dropIndex(self::BROKEN_INDEX);
            });
        } catch (Throwable) {
            // Index tidak ada pada database yang belum pernah membuatnya.
        }

        Schema::table('rekomendasi_log', function (Blueprint $table): void {
            $table->index(['user_id', 'model_version', 'created_at'], self::FIXED_INDEX);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('rekomendasi_log')) {
            return;
        }

        try {
            Schema::table('rekomendasi_log', function (Blueprint $table): void {
                $table->dropIndex(self::FIXED_INDEX);
            });
        } catch (Throwable) {
            // Index tidak ada, tidak ada yang perlu dibatalkan.
        }
    }
};
