<?php

namespace App\Filament\Resources\KategoriMinatResource\Pages;

use App\Filament\Resources\KategoriMinatResource;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriMinat extends ViewRecord
{
    protected static string $resource = KategoriMinatResource::class;

    public function getBreadcrumb(): string
    {
        return 'Lihat';
    }
}
