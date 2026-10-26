<?php

namespace App\Filament\Resources\InstitusiResource\Pages;

use App\Filament\Resources\InstitusiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInstitusi extends CreateRecord
{
    protected static string $resource = InstitusiResource::class;

    public function getBreadcrumb(): string
    {
        return 'Buat';
    }
}
