<?php

namespace App\Filament\Resources\InstitusiResource\Pages;

use App\Filament\Resources\InstitusiResource;
use Filament\Resources\Pages\ViewRecord;

class ViewInstitusi extends ViewRecord
{
    protected static string $resource = InstitusiResource::class;

    public function getBreadcrumb(): string
    {
        return 'Lihat';
    }
}
