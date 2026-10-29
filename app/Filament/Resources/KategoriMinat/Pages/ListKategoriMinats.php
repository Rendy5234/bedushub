<?php

namespace App\Filament\Resources\KategoriMinat\Pages;

use App\Filament\Resources\KategoriMinat\KategoriMinatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKategoriMinats extends ListRecords
{
    protected static string $resource = KategoriMinatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
