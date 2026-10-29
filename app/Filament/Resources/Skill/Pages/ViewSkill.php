<?php

namespace App\Filament\Resources\Skill\Pages;

use App\Filament\Resources\Skill\SkillResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSkill extends ViewRecord
{
    protected static string $resource = SkillResource::class;

    public function getBreadcrumb(): string
    {
        return 'Lihat';
    }
}
