<?php

namespace App\Filament\Resources\Institusi\Pages;

use App\Filament\Resources\Institusi\InstitusiResource;
use Filament\Resources\Pages\ViewRecord;

class ViewInstitusi extends ViewRecord
{
    protected static string $resource = InstitusiResource::class;

    public function getBreadcrumb(): string
    {
        return 'Lihat';
    }
}
