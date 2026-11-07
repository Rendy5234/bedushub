<?php

namespace App\Filament\Resources\Skill\Pages;

use App\Filament\Resources\Skill\SkillResource;
use App\Models\Skill;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSkill extends EditRecord
{
    protected static string $resource = SkillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RestoreAction::make()
                ->hidden(fn (Skill $record): bool => ! $record->trashed()),
            DeleteAction::make()
                ->disabled(fn (Skill $record): bool => $record->userSkills()->exists()
                    || $record->lowonganSkills()->exists()
                    || $record->pelatihanSkills()->exists())
                ->tooltip(fn (Skill $record): ?string => ($record->userSkills()->exists()
                    || $record->lowonganSkills()->exists()
                    || $record->pelatihanSkills()->exists())
                    ? 'Tidak bisa dihapus karena sedang digunakan'
                    : null)
                ->hidden(fn (Skill $record): bool => $record->trashed()),
            ForceDeleteAction::make()
                ->hidden(fn (Skill $record): bool => ! $record->trashed()),
        ];
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }
}
