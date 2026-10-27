<?php

namespace App\Filament\Resources\InstitusiResource\Pages;

use App\Filament\Imports\InstitusiImporter;
use App\Filament\Resources\InstitusiResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\FailedImportRow;
use Filament\Actions\Imports\Models\Import;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class ListInstitusi extends ListRecords
{
    /**
     * Jumlah baris "Data Gagal" per halaman pada modal Hasil Import.
     */
    public const HASIL_IMPORT_PER_PAGE = 10;

    protected static string $resource = InstitusiResource::class;

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
            ->where('importer', InstitusiImporter::class)
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
                ->importer(InstitusiImporter::class)
                ->label('Import Institusi')
                ->icon(Heroicon::OutlinedArrowUpTray)
                // Menghilangkan link bawaan Filament "Download example CSV file".
                // Di Filament 5 link tersebut adalah modal action `downloadExample`
                // yang dipasang sebagai modalDescription (ImportAction::setUp()).
                ->modalDescription(null)
                ->maxRows(1000),
            Action::make('download_template')
                ->label('Unduh Template')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->url(asset('templates/template-institusi.csv'))
                ->openUrlInNewTab(),
            Action::make('hasil_import')
                ->label('Hasil Import')
                ->icon(Heroicon::OutlinedQueueList)
                ->color('gray')
                ->modalHeading('Hasil Import Institusi Terakhir')
                // Selalu mulai dari halaman pertama setiap kali modal dibuka.
                ->mountUsing(function (): void {
                    $this->hasilImportPage = 1;
                })
                ->modalContent(function (): View {
                    $import = Import::query()
                        ->where('importer', InstitusiImporter::class)
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
