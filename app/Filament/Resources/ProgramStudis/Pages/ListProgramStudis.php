<?php

namespace App\Filament\Resources\ProgramStudis\Pages;

use App\Filament\Imports\ProgramStudiImporter;
use App\Filament\Resources\ProgramStudis\ProgramStudiResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\FailedImportRow;
use Filament\Actions\Imports\Models\Import;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class ListProgramStudis extends ListRecords
{
    /**
     * Jumlah baris "Data Gagal" per halaman pada modal Hasil Import.
     */
    public const HASIL_IMPORT_PER_PAGE = 10;

    protected static string $resource = ProgramStudiResource::class;

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
            ->where('importer', ProgramStudiImporter::class)
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
                ->importer(ProgramStudiImporter::class)
                ->label('Import Program Studi')
                ->icon(Heroicon::OutlinedArrowUpTray)
                // Menghilangkan link bawaan Filament "Download example CSV file".
                ->modalDescription(null)
                ->maxRows(1000),
            Action::make('download_template')
                ->label('Unduh Template')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->url(asset('templates/template-prodi.csv'))
                ->openUrlInNewTab(),
            Action::make('hasil_import')
                ->label('Hasil Import')
                ->icon(Heroicon::OutlinedQueueList)
                ->color('gray')
                ->modalHeading('Hasil Import Program Studi Terakhir')
                // Selalu mulai dari halaman pertama setiap kali modal dibuka.
                ->mountUsing(function (): void {
                    $this->hasilImportPage = 1;
                })
                ->modalContent(function (): View {
                    $import = Import::query()
                        ->where('importer', ProgramStudiImporter::class)
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
