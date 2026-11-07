<?php

namespace App\Filament\Resources\ProgramStudi\Pages;

use App\Filament\Resources\ProgramStudi\ProgramStudiResource;
use App\Models\ProgramStudi;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProgramStudi extends EditRecord
{
    protected static string $resource = ProgramStudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (ProgramStudi $record): bool => $record->trashed()),
            ForceDeleteAction::make()
                ->hidden(fn (ProgramStudi $record): bool => ! $record->trashed()),
            RestoreAction::make()
                ->hidden(fn (ProgramStudi $record): bool => ! $record->trashed()),
        ];
    }
}
