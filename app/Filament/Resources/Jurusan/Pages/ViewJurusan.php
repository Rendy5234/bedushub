<?php

namespace App\Filament\Resources\Jurusan\Pages;

use App\Filament\Resources\Jurusan\JurusanResource;
use Filament\Resources\Pages\ViewRecord;

class ViewJurusan extends ViewRecord
{
    protected static string $resource = JurusanResource::class;

    public function getBreadcrumb(): string
    {
        return $this->record->nama;
    }
}
