<?php

namespace Tests\Feature\Imports;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator as LaraPaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Verifikasi rendering modal "Hasil Import Institusi Terakhir" (Blade).
 *
 * Fokus: statistik ringkas, tabel "Data Gagal" (Data Asli / Pesan Error),
 * scroll horizontal mobile, dan navigasi halaman server-side (tanpa Alpine.js).
 * View di-render secara langsung (white-box) tanpa otentikasi panel Filament.
 */
class InstitusiImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  Collection<int, object{data: array, validation_error: string|null}>  $items
     */
    private static function fakePaginator(Collection $items, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return new LaraPaginator(
            $items->values()->all(),
            $items->count(),
            $perPage,
            $page,
            ['path' => '/admin/institusi'],
        );
    }

    private function renderView(
        ?Import $import = null,
        LengthAwarePaginator|Collection|null $failedRows = null,
    ): string {
        return view('filament.actions.hasil-import', [
            'import' => $import,
            'failedRows' => $failedRows ?? collect(),
        ])->render();
    }

    public function test_modal_shows_no_history_message_when_import_is_null(): void
    {
        $html = $this->renderView(null);

        $this->assertStringContainsString('Belum ada riwayat import institusi', $html);
        $this->assertStringNotContainsString('Data Berhasil Diproses', $html);
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

        // stdClass memakai interface yang sama dengan FailedImportRow: data + validation_error.
        $failedRow = new \stdClass;
        $failedRow->data = ['nama' => 'Akademi XYZ, AKADEMI, Bojonegoro, Jl. Test, aktif'];
        $failedRow->validation_error = 'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).';

        $html = $this->renderView($import, $this->fakePaginator(collect([$failedRow])));

        $this->assertStringContainsString('Data Gagal', $html);
        $this->assertStringContainsString('Data Asli', $html);
        $this->assertStringContainsString('Pesan Error', $html);
        $this->assertStringContainsString('custom-modal-text', $html);
        $this->assertStringNotContainsString('var(--gray-950)', $html);
        $this->assertStringContainsString('Akademi XYZ, AKADEMI, Bojonegoro, Jl. Test, aktif', $html);
        $this->assertStringContainsString('Format CSV tidak sesuai', $html);
        // Alpine.js sudah dihapus dari Blade ini.
        $this->assertStringNotContainsString('x-text', $html);
        $this->assertStringNotContainsString('x-data', $html);
        $this->assertStringNotContainsString('x-for', $html);
    }

    public function test_modal_shows_no_failed_message_when_no_failed_rows(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 1,
            'successful_rows' => 1,
            'completed_at' => now(),
        ]);

        $html = $this->renderView($import, $this->fakePaginator(collect()));

        $this->assertStringContainsString('Tidak ada data gagal', $html);
    }

    public function test_modal_wraps_table_in_horizontal_scroll_container_for_mobile(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 1,
            'successful_rows' => 0,
            'completed_at' => now(),
        ]);

        $failedRow = new \stdClass;
        $failedRow->data = ['nama' => 'Baris, Test'];
        $failedRow->validation_error = 'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).';

        $html = $this->renderView($import, $this->fakePaginator(collect([$failedRow])));

        // Wrapper scroll horizontal + min-width tabel agar layout mobile tidak mengecil.
        $this->assertStringContainsString('overflow-x: auto', $html);
        $this->assertStringContainsString('-webkit-overflow-scrolling: touch', $html);
        $this->assertStringContainsString('min-width: 600px', $html);
        // Tweak manual user: lebar kolom 40% / 60% dan <ul> error tanpa padding-left.
        $this->assertStringContainsString('width: 40%', $html);
        $this->assertStringContainsString('width: 60%', $html);
        $this->assertStringContainsString('<ul style="margin: 0;', $html);
    }

    public function test_modal_shows_page_navigation_for_paginated_failed_rows(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 25,
            'successful_rows' => 0,
            'completed_at' => now(),
        ]);

        $items = [];

        for ($i = 11; $i <= 20; $i++) {
            $row = new \stdClass;
            $row->data = ['nama' => 'Baris '.$i.', Test'];
            $row->validation_error = 'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).';
            $items[] = $row;
        }

        // 25 baris total, 10 per halaman => 3 halaman, sedang di halaman 2.
        $failedRows = new LaraPaginator($items, 25, 10, 2, ['path' => 'http://localhost/admin/institusi']);

        $html = $this->renderView($import, $failedRows);

        $this->assertStringContainsString('Sebelumnya', $html);
        $this->assertStringContainsString('Berikutnya', $html);
        $this->assertStringContainsString('Halaman 2 dari 3', $html);
        $this->assertStringContainsString('Menampilkan 11-20', $html);
        $this->assertStringContainsString('dari 25 baris', $html);
        // Navigasi memakai method paginator Laravel, bukan Alpine.js.
        $this->assertStringContainsString('wire:click.prevent="previousHasilImportPage"', $html);
        $this->assertStringContainsString('wire:click.prevent="nextHasilImportPage"', $html);
        $this->assertStringContainsString('href="http://localhost/admin/institusi?page=1"', $html);
        $this->assertStringContainsString('href="http://localhost/admin/institusi?page=3"', $html);
    }

    public function test_modal_disables_navigation_buttons_on_single_page(): void
    {
        $import = new Import([
            'file_name' => 'template-institusi.csv',
            'total_rows' => 5,
            'successful_rows' => 0,
            'completed_at' => now(),
        ]);

        $failedRow = new \stdClass;
        $failedRow->data = ['nama' => 'Baris, Test'];
        $failedRow->validation_error = 'Format CSV tidak sesuai. Pastikan dipisahkan dengan koma (,).';

        $failedRows = new LaraPaginator([$failedRow], 5, 10, 1, ['path' => '/admin/institusi']);

        $html = $this->renderView($import, $failedRows);

        $this->assertStringContainsString('Halaman 1 dari 1', $html);
        $this->assertStringNotContainsString('previousHasilImportPage', $html);
        $this->assertStringNotContainsString('nextHasilImportPage', $html);
        $this->assertStringContainsString('aria-disabled="true"', $html);
    }
}
