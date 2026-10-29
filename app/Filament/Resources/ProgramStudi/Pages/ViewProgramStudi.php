<?php

namespace App\Filament\Resources\ProgramStudi\Pages;

use App\Filament\Resources\ProgramStudi\ProgramStudiResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramStudi extends ViewRecord
{
    protected static string $resource = ProgramStudiResource::class;

    public function getBreadcrumb(): string
    {
        return 'Lihat';
    }
}
