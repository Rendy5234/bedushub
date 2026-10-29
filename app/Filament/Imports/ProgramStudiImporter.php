<?php

namespace App\Filament\Imports;

use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

class ProgramStudiImporter extends Importer
{
    protected static ?string $model = ProgramStudi::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama')
                ->label('Nama Program Studi')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('kode')
                ->label('Kode')
                ->rules(['nullable', 'string', 'max:20']),
            ImportColumn::make('jenjang')
                ->label('Jenjang')
                ->requiredMapping()
                ->rules(['required', 'string', 'in:D3,S1,S2,S3']),
            ImportColumn::make('fakultas_id')
                ->label('Fakultas')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255', Rule::exists('fakultas', 'id')])
                ->castStateUsing(function (mixed $state): ?string {
                    if (! filled($state)) {
                        return null;
                    }

                    $fakultas = Fakultas::withTrashed()
                        ->where('nama', (string) $state)
                        ->first();

                    return $fakultas?->id;
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
        if (
            filled($this->data['kode'] ?? null)
            || filled($this->data['jenjang'] ?? null)
            || filled($this->data['fakultas_id'] ?? null)
        ) {
            return;
        }

        // Escape dikosongkan agar pemecahan mengikuti RFC 4180 (tanpa escape backslash).
        $fields = array_map(
            fn (string $field): string => trim($field, "\"\t\n\r\0\x0B"),
            str_getcsv(trim($nama, "\"\t\n\r\0\x0B"), ',', '"', ''),
        );

        if (count($fields) < 4) {
            return;
        }

        [$namaProdi, $kode, $jenjang, $fakultasNama] = $fields;

        $this->data['nama'] = $namaProdi;
        $this->data['kode'] = filled($kode) ? $kode : null;
        $this->data['jenjang'] = $jenjang;
        $this->data['fakultas_id'] = $fakultasNama;

        // Jalankan ulang cast kolom sebelum masuk validator.
        $this->castData();
    }

    /**
     * Cari program studi berdasarkan nama + fakultas, termasuk data yang sudah di-soft delete.
     */
    public function resolveRecord(): ?ProgramStudi
    {
        // Normalisasi lebih dulu agar pencarian memakai nama yang sudah bersih
        // dan tidak membuat duplikat saat baris rusak.
        $this->normalizeCorruptedRow();

        $fakultasId = $this->data['fakultas_id'] ?? null;

        // Relasi yang tidak ditemukan tetap menghasilkan record baru dengan
        // foreign key null. Baris tersebut lalu gagal di tahap validasi dan
        // tercatat di Failed Rows, bukan melempar exception.
        return ProgramStudi::withTrashed()->firstOrNew([
            'nama' => (string) ($this->data['nama'] ?? ''),
            'fakultas_id' => filled($fakultasId) ? (string) $fakultasId : null,
        ]);
    }

    /**
     * Pulihkan data yang sebelumnya terhapus setelah baris lolos validasi & tersimpan.
     */
    public function afterSave(): void
    {
        if ($this->record instanceof ProgramStudi && $this->record->trashed()) {
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
            'fakultas_id.required' => 'Fakultas tidak ditemukan. Periksa nama fakultas pada baris ini.',
            'fakultas_id.exists' => 'Fakultas tidak ditemukan. Periksa nama fakultas pada baris ini.',
            'jenjang.required' => 'Kolom jenjang tidak ditemukan. Periksa susunan CSV.',
            'jenjang.in' => 'Jenjang tidak valid. Gunakan: D3, S1, S2, atau S3.',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import program studi selesai. '.number_format($import->successful_rows).' baris berhasil diproses.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal.';
        }

        return $body;
    }
}
