<?php

namespace App\Filament\Resources\SkillResource\Pages;

use App\Filament\Resources\SkillResource;
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
            RestoreAction::make(),
            DeleteAction::make()
                ->hidden(fn (Skill $record): bool => $record->userSkills()->exists()
                    || $record->lowonganSkills()->exists()
                    || $record->pelatihanSkills()->exists()
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
