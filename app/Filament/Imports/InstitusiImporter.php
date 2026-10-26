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
     * Pre-processing data sebelum divalidasi.
     *
     * Excel (terutama locale Indonesia) sering membungkus seluruh baris ke dalam
     * satu pasang tanda kutip ganda ketika ada koma di dalam sel (misalnya
     * alamat). Parser CSV membuang kutip terluar tersebut, sehingga baris rusak
     * terbaca sebagai satu nilai di kolom "nama" dan kolom lainnya kosong.
     *
     * Contoh baris rusak di file:
     * "SMK Negeri 1 Bedus,SMK,Bojonegoro,""Jl. Raya Bedus No. 1, Bojonegoro"",aktif"
     *
     * Nilai yang diterima hook ini:
     * SMK Negeri 1 Bedus,SMK,Bojonegoro,"Jl. Raya Bedus No. 1, Bojonegoro",aktif
     */
    public function beforeValidate(): void
    {
        $this->normalizeExcelCorruptedRow();
    }

    /**
     * Pecah kembali baris yang terbungkus kutip ganda oleh Excel menjadi kolom terpisah.
     */
    protected function normalizeExcelCorruptedRow(): void
    {
        $nama = $this->data['nama'] ?? null;

        if (! is_string($nama) || ! str_contains($nama, ',')) {
            return;
        }

        // Baris rusak selalu terbaca sebagai satu kolom di "nama", sedangkan
        // kolom lain kosong. Jika kolom lain terisi, ini baris normal.
        if (
            filled($this->data['jenis'] ?? null)
            || filled($this->data['kecamatan'] ?? null)
            || filled($this->data['alamat'] ?? null)
        ) {
            return;
        }

        // Escape dikosongkan agar pemecahan mengikuti RFC 4180 (tanpa escape backslash).
        $fields = array_map(
            fn (string $field): string => trim($field, "\" \t\n\r\0\x0B"),
            str_getcsv(trim($nama, "\" \t\n\r\0\x0B"), ',', '"', ''),
        );

        if (count($fields) < 5) {
            return;
        }

        // Kolom terakhir adalah status, sisa kolom setelah kecamatan adalah alamat
        // (alamat bisa mengandung koma yang tidak dibungkus kutip oleh Excel).
        $status = (string) array_pop($fields);
        [$namaInstitusi, $jenis, $kecamatan] = $fields;
        $alamat = implode(', ', array_slice($fields, 3));

        $this->data['nama'] = $namaInstitusi;
        $this->data['jenis'] = $jenis;
        $this->data['kecamatan'] = $kecamatan;
        $this->data['alamat'] = filled($alamat) ? $alamat : null;
        $this->data['is_active'] = $status;

        // Jalankan ulang cast kolom agar "jenis" menjadi uppercase dan
        // "is_active" dikonversi ke boolean sebelum masuk validator.
        $this->castData();
    }

    /**
     * Cari institusi berdasarkan nama, termasuk data yang sudah di-soft delete.
     */
    public function resolveRecord(): ?Institusi
    {
        // Normalisasi lebih dulu agar pencarian memakai nama yang sudah bersih
        // dan tidak membuat duplikat saat baris berasal dari Excel.
        $this->normalizeExcelCorruptedRow();

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
