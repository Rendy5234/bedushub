<?php

namespace App\Filament\Resources\KategoriMinat\Pages;

use App\Filament\Resources\KategoriMinat\KategoriMinatResource;
use App\Models\KategoriMinat;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditKategoriMinat extends EditRecord
{
    protected static string $resource = KategoriMinatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RestoreAction::make()
                ->hidden(fn (KategoriMinat $record): bool => ! $record->trashed()),
            DeleteAction::make()
                ->disabled(fn (KategoriMinat $record): bool => $record->userMinat()->exists()
                    || $record->lowonganMinat()->exists()
                    || $record->pelatihanMinat()->exists())
                ->tooltip(fn (KategoriMinat $record): ?string => ($record->userMinat()->exists()
                    || $record->lowonganMinat()->exists()
                    || $record->pelatihanMinat()->exists())
                    ? 'Tidak bisa dihapus karena sedang digunakan'
                    : null)
                ->hidden(fn (KategoriMinat $record): bool => $record->trashed()),
            ForceDeleteAction::make()
                ->hidden(fn (KategoriMinat $record): bool => ! $record->trashed()),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }
}
