<?php

namespace App\Filament\Resources\InstitusiResource\Pages;

use App\Filament\Imports\InstitusiImporter;
use App\Filament\Resources\InstitusiResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\Import;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;

class ListInstitusi extends ListRecords
{
    protected static string $resource = InstitusiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(InstitusiImporter::class)
                ->label('Import Institusi')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalDescription('PENTING: Jangan mengedit file CSV menggunakan Microsoft Excel karena dapat merusak format koma. Gunakan teks editor atau Google Sheets.')
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
                ->modalContent(function (): View {
                    $import = Import::query()
                        ->where('importer', InstitusiImporter::class)
                        ->latest()
                        ->first();

                    return view('filament.actions.hasil-import', [
                        'import' => $import,
                        'failedRows' => $import?->failedRows()->latest()->limit(10)->get(),
                        'successfulRows' => collect(json_decode(
                            $import?->successful_row_data ?? '[]',
                            true,
                            512,
                            JSON_THROW_ON_ERROR,
                        ))->filter(fn ($row): bool => is_array($row))->values(),
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
        ];
    }
}
