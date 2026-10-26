<?php

namespace App\Filament\Imports;

use App\Enums\JenisInstitusi;
use App\Models\Institusi;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

class InstitusiImporter extends Importer
{
    protected static ?string $model = Institusi::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('jenis')
                ->label('Jenis')
                ->requiredMapping()
                ->castStateUsing(fn (mixed $state): ?string => filled($state)
                    ? strtoupper(trim((string) $state))
                    : null)
                ->rules(['required', Rule::in(array_map(
                    fn (JenisInstitusi $jenis): string => $jenis->value,
                    JenisInstitusi::cases(),
                ))]),
            ImportColumn::make('kecamatan')
                ->label('Kecamatan')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('alamat')
                ->label('Alamat')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('is_active')
                ->label('Status')
                ->guess(['status'])
                ->castStateUsing(fn (mixed $state): bool => blank($state)
                    || in_array(strtolower(trim((string) $state)), ['1', 'aktif', 'active', 'ya', 'yes', 'true'], true))
                ->rules(['boolean']),
        ];
    }

    /**
     * Cari institusi berdasarkan nama, termasuk data yang sudah di-soft delete.
     */
    public function resolveRecord(): ?Institusi
    {
        return Institusi::withTrashed()->firstOrNew([
            'nama' => (string) ($this->data['nama'] ?? ''),
        ]);
    }

    /**
     * Pulihkan data yang sebelumnya terhapus setelah baris lolos validasi & tersimpan.
     */
    public function afterSave(): void
    {
        if ($this->record instanceof Institusi && $this->record->trashed()) {
            $this->record->restore();
        }
    }

    /**
     * Pesan validasi yang menjelaskan penyebab umum kegagalan import (CSV rusak/Excel).
     *
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        return [
            'nama.required' => 'Kolom "nama" tidak terbaca pada baris ini. Pastikan file CSV dipisahkan dengan koma (,), bukan titik koma (;), dan tidak disimpan ulang menggunakan Microsoft Excel.',
            'jenis.required' => 'Kolom "jenis" tidak terbaca pada baris ini. Pastikan file CSV dipisahkan dengan koma (,), setiap baris berisi 5 kolom (nama, jenis, kecamatan, alamat, status), dan tidak diedit menggunakan Microsoft Excel.',
            'jenis.in' => 'Nilai kolom "jenis" tidak valid. Gunakan salah satu: SMK, SMA, atau PT.',
            'kecamatan.max' => 'Kolom "kecamatan" maksimal 255 karakter.',
            'alamat.max' => 'Kolom "alamat" maksimal 255 karakter.',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import institusi selesai. '.number_format($import->successful_rows).' baris berhasil diproses.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal.';
        }

        return $body;
    }
}
