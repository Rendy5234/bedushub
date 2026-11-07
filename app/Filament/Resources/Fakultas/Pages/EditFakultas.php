<?php

namespace App\Filament\Resources\Fakultas\Pages;

use App\Filament\Resources\Fakultas\FakultasResource;
use App\Models\Fakultas;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditFakultas extends EditRecord
{
    protected static string $resource = FakultasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (Fakultas $record): bool => $record->hasProgramStudi())
                ->tooltip(fn (Fakultas $record): ?string => $record->hasProgramStudi()
                    ? 'Tidak bisa dihapus karena masih memiliki Program Studi'
                    : null)
                ->hidden(fn (Fakultas $record): bool => $record->trashed()),
            ForceDeleteAction::make()
                ->hidden(fn (Fakultas $record): bool => ! $record->trashed()),
            RestoreAction::make()
                ->hidden(fn (Fakultas $record): bool => ! $record->trashed()),
        ];
    }
}
