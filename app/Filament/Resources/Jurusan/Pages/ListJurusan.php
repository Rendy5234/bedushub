<?php

namespace App\Filament\Resources\Jurusan\Pages;

use App\Filament\Imports\JurusanImporter;
use App\Filament\Resources\Jurusan\JurusanResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\FailedImportRow;
use Filament\Actions\Imports\Models\Import;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class ListJurusan extends ListRecords
{
    /**
     * Jumlah baris "Data Gagal" per halaman pada modal Hasil Import.
     */
    public const HASIL_IMPORT_PER_PAGE = 10;

    protected static string $resource = JurusanResource::class;

    /**
     * Halaman aktif untuk tabel "Data Gagal" pada modal Hasil Import.
     *
     * Disimpan sebagai public property Livewire supaya navigasi halaman bisa
     * berjalan tanpa Alpine.js dan tanpa memuat ulang halaman (modal tetap terbuka).
     */
    public int $hasilImportPage = 1;

    public function previousHasilImportPage(): void
    {
        $this->hasilImportPage = max(1, $this->hasilImportPage - 1);
    }

    public function nextHasilImportPage(): void
    {
        $failedRowsCount = Import::query()
            ->where('importer', JurusanImporter::class)
            ->latest()
            ->first()
            ?->getFailedRowsCount() ?? 0;

        $lastPage = max(1, (int) ceil($failedRowsCount / self::HASIL_IMPORT_PER_PAGE));

        $this->hasilImportPage = min($lastPage, $this->hasilImportPage + 1);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(JurusanImporter::class)
                ->label('Import Jurusan')
                ->icon(Heroicon::OutlinedArrowUpTray)
                // Menghilangkan link bawaan Filament "Download example CSV file".
                ->modalDescription(null)
                ->maxRows(1000),
            Action::make('download_template')
                ->label('Unduh Template')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->url(asset('templates/template-jurusan.csv'))
                ->openUrlInNewTab(),
            Action::make('hasil_import')
                ->label('Hasil Import')
                ->icon(Heroicon::OutlinedQueueList)
                ->color('gray')
                ->modalHeading('Hasil Import Jurusan Terakhir')
                // Selalu mulai dari halaman pertama setiap kali modal dibuka.
                ->mountUsing(function (): void {
                    $this->hasilImportPage = 1;
                })
                ->modalContent(function (): View {
                    $import = Import::query()
                        ->where('importer', JurusanImporter::class)
                        ->latest()
                        ->first();

                    /** @var LengthAwarePaginator<int, FailedImportRow>|null $failedRows */
                    $failedRows = $import?->failedRows()
                        ->latest()
                        ->paginate(self::HASIL_IMPORT_PER_PAGE, ['*'], 'hasilImportPage', $this->hasilImportPage)
                        // Path eksplisit supaya href navigasi mengarah ke halaman resource,
                        // bukan ke endpoint /livewire/update.
                        ->withPath(static::getUrl());

                    return view('filament.actions.hasil-import', [
                        'import' => $import,
                        'failedRows' => $failedRows,
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
        ];
    }
}
