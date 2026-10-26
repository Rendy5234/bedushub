<?php

namespace App\Filament\Resources\InstitusiResource\Pages;

use App\Filament\Resources\InstitusiResource;
use App\Models\Institusi;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditInstitusi extends EditRecord
{
    protected static string $resource = InstitusiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RestoreAction::make(),
            DeleteAction::make()
                ->disabled(fn (Institusi $record): bool => $record->hasFakultasOrJurusan())
                ->tooltip(fn (Institusi $record): ?string => $record->hasFakultasOrJurusan()
                    ? 'Tidak bisa dihapus karena masih memiliki Fakultas atau Jurusan'
                    : null),
            ForceDeleteAction::make(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }
}
