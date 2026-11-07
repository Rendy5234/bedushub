<?php

namespace App\Filament\Resources\Jurusan\Pages;

use App\Filament\Resources\Jurusan\JurusanResource;
use App\Models\Jurusan;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditJurusan extends EditRecord
{
    protected static string $resource = JurusanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (Jurusan $record): bool => $record->trashed()),
            ForceDeleteAction::make()
                ->hidden(fn (Jurusan $record): bool => ! $record->trashed()),
            RestoreAction::make()
                ->hidden(fn (Jurusan $record): bool => ! $record->trashed()),
        ];
    }
}
