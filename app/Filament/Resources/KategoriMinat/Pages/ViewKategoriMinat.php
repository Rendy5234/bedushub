<?php

namespace App\Filament\Resources\KategoriMinat\Pages;

use App\Filament\Resources\KategoriMinat\KategoriMinatResource;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriMinat extends ViewRecord
{
    protected static string $resource = KategoriMinatResource::class;

    public function getBreadcrumb(): string
    {
        return 'Lihat';
    }
}
