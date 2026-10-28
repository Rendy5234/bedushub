<?php

namespace App\Filament\Imports;

use App\Models\Fakultas;
use App\Models\Institusi;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class FakultasImporter extends Importer
{
    protected static ?string $model = Fakultas::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama')
                ->label('Nama Fakultas')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('institusi_nama')
                ->label('Institusi')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->castStateUsing(function (mixed $state): ?string {
                    if (! filled($state)) {
                        return null;
                    }

                    $institusi = Institusi::withTrashed()
                        ->where('nama', (string) $state)
                        ->first();

                    return $institusi?->id;
                }),
        ];
    }

    /**
     * Pre-processing data sebelum divalidasi.
     *
     * Aplikasi spreadsheet (terutama locale Indonesia) sering membungkus seluruh
     * baris ke dalam satu pasang tanda kutip ganda ketika ada koma di dalam sel.
     * Parser CSV membuang kutip terluar tersebut, sehingga baris rusak terbaca
     * sebagai satu nilai di kolom "nama" dan kolom lain kosong.
     */
    public function beforeValidate(): void
    {
        $this->normalizeCorruptedRow();
    }

    /**
     * Pecah kembali baris yang terbungkus kutip ganda menjadi kolom terpisah.
     */
    protected function normalizeCorruptedRow(): void
    {
        $nama = $this->data['nama'] ?? null;

        if (! is_string($nama) || ! str_contains($nama, ',')) {
            return;
        }

        // Baris rusak selalu terbaca sebagai satu kolom di "nama", sedangkan
        // kolom lain kosong. Jika kolom lain terisi, ini baris normal.
        if (filled($this->data['institusi_nama'] ?? null)) {
            return;
        }

        // Escape dikosongkan agar pemecahan mengikuti RFC 4180 (tanpa escape backslash).
        $fields = array_map(
            fn (string $field): string => trim($field, "\" \t\n\r\0\x0B"),
            str_getcsv(trim($nama, "\" \t\n\r\0\x0B"), ',', '"', ''),
        );

        if (count($fields) < 2) {
            return;
        }

        [$namaFakultas, $institusiNama] = $fields;

        $this->data['nama'] = $namaFakultas;
        $this->data['institusi_nama'] = $institusiNama;

        // Jalankan ulang cast kolom sebelum masuk validator.
        $this->castData();
    }

    /**
     * Cari fakultas berdasarkan nama + institusi, termasuk data yang sudah di-soft delete.
     */
    public function resolveRecord(): ?Fakultas
    {
        // Normalisasi lebih dulu agar pencarian memakai nama yang sudah bersih
        // dan tidak membuat duplikat saat baris rusak.
        $this->normalizeCorruptedRow();

        $institusiId = (string) ($this->data['institusi_nama'] ?? '');

        return Fakultas::withTrashed()->firstOrNew([
            'nama' => (string) ($this->data['nama'] ?? ''),
            'institusi_id' => $institusiId,
        ]);
    }

    /**
     * Pulihkan data yang sebelumnya terhapus setelah baris lolos validasi & tersimpan.
     */
    public function afterSave(): void
    {
        if ($this->record instanceof Fakultas && $this->record->trashed()) {
            $this->record->restore();
        }
    }

    /**
     * Pesan validasi yang singkat dan netral untuk kegagalan import CSV.
     *
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        return [
            'nama.required' => 'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).',
            'institusi_nama.required' => 'Kolom institusi tidak ditemukan. Periksa susunan CSV.',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import fakultas selesai. '.number_format($import->successful_rows).' baris berhasil diproses.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal.';
        }

        return $body;
    }
}
