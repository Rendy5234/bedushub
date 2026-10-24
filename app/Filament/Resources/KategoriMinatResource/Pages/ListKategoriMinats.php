<?php

namespace App\Filament\Resources\KategoriMinatResource\Pages;

use App\Filament\Resources\KategoriMinatResource;
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
