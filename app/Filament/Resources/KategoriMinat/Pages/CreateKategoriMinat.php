<?php

namespace App\Filament\Resources\KategoriMinat\Pages;

use App\Filament\Resources\KategoriMinat\KategoriMinatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoriMinat extends CreateRecord
{
    protected static string $resource = KategoriMinatResource::class;

    public function getBreadcrumb(): string
    {
        return 'Buat';
    }
}
