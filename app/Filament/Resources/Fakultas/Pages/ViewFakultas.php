<?php

namespace App\Filament\Resources\Fakultas\Pages;

use App\Filament\Resources\Fakultas\FakultasResource;
use Filament\Resources\Pages\ViewRecord;

class ViewFakultas extends ViewRecord
{
    protected static string $resource = FakultasResource::class;

    public function getBreadcrumb(): string
    {
        return 'Lihat';
    }
}
