<?php

namespace App\Filament\Resources\KategoriMinatResource\Pages;

use App\Filament\Resources\KategoriMinatResource;
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
            RestoreAction::make(),
            DeleteAction::make()
                ->hidden(fn (KategoriMinat $record): bool => $record->userMinat()->exists()
                    || $record->lowonganMinat()->exists()
                    || $record->pelatihanMinat()->exists()
                )
                ->tooltip('Tidak bisa dihapus karena sedang digunakan'),
            ForceDeleteAction::make(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }
}
