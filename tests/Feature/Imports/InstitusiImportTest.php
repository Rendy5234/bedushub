<?php

namespace Tests\Feature\Imports;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Verifikasi rendering modal "Hasil Import Institusi Terakhir" (Blade).
 *
 * Fokus: tabel "Data Berhasil Diproses" (Nama/Jenis/Kecamatan) dan tabel
 * "Data Gagal" (Data Asli/Pesan Error). View di-render secara langsung
 * (white-box) tanpa harus melewati otentikasi panel Filament agar cepat.
 */
class InstitusiImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  Collection<int, object{data: array, validation_error: string|null}>|null  $failedRows
     * @param  Collection<int, array{nama: string, jenis: string, kecamatan: string}>|null  $successfulRows
     */
    private function renderView(
        ?Import $import = null,
        ?Collection $failedRows = null,
        ?Collection $successfulRows = null,
    ): string {
        return view('filament.actions.hasil-import', [
            'import' => $import,
            'failedRows' => $failedRows ?? collect(),
            'successfulRows' => $successfulRows ?? collect(),
        ])->render();
    }

    public function test_modal_shows_no_history_message_when_import_is_null(): void
    {
        $html = $this->renderView(null);

        $this->assertStringContainsString('Belum ada riwayat import institusi', $html);
        $this->assertStringNotContainsString('Data Berhasil Diproses', $html);
        $this->assertStringNotContainsString('Data Gagal', $html);
    }

    public function test_modal_displays_success_rows_with_nama_jenis_kecamatan(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 2,
            'successful_rows' => 2,
            'completed_at' => now(),
        ]);

        $successfulRows = collect([
            ['nama' => 'SMK Negeri 1 Bedus', 'jenis' => 'SMK', 'kecamatan' => 'Bojonegoro'],
            ['nama' => 'SMA Negeri 1 Kota', 'jenis' => 'SMA', 'kecamatan' => 'Kalitenang'],
        ]);

        $html = $this->renderView($import, collect(), $successfulRows);

        $this->assertStringContainsString('Data Berhasil Diproses', $html);
        $this->assertStringContainsString('SMK Negeri 1 Bedus', $html);
        $this->assertStringContainsString('Bojonegoro', $html);
        $this->assertStringNotContainsString('Belum ada data berhasil', $html);
        $this->assertStringNotContainsString('Data Gagal', $html);
    }

    public function test_modal_displays_failed_rows_with_data_and_error_message(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 2,
            'successful_rows' => 0,
            'completed_at' => now(),
        ]);

        // Use stdClass to mimic FailedImportRow interface (data + validation_error).
        $failedRow = new \stdClass;
        $failedRow->data = ['nama' => 'Akademi XYZ', 'jenis' => 'AKADEMI'];
        $failedRow->validation_error = 'Nilai kolom "jenis" tidak valid. Gunakan salah satu: SMK, SMA, atau PT.';

        $failedRows = collect([$failedRow]);

        $html = $this->renderView($import, $failedRows);

        $this->assertStringContainsString('Data Gagal', $html);
        $this->assertStringContainsString('Data Asli', $html);
        $this->assertStringContainsString('Pesan Error', $html);
        $this->assertStringContainsString('Akademi XYZ', $html);
        $this->assertStringContainsString('AKADEMI', $html);
        $this->assertStringContainsString('tidak valid', $html);
    }

    public function test_modal_shows_empty_success_message_when_no_success_rows(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 1,
            'successful_rows' => 0,
            'completed_at' => now(),
        ]);

        $html = $this->renderView($import, collect(), collect());

        $this->assertStringContainsString('Belum ada data berhasil', $html);
    }
}
