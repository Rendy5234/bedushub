<?php

namespace App\Filament\Resources\KategoriMinatResource\Pages;

use App\Filament\Resources\KategoriMinatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoriMinat extends CreateRecord
{
    protected static string $resource = KategoriMinatResource::class;

    public function getBreadcrumb(): string
    {
        return 'Buat';
    }
}
