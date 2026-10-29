<?php

namespace App\Filament\Resources\KategoriMinat\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KategoriMinatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori Minat')
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama')
                                ->label('Nama Kategori Minat')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('deskripsi')
                                ->label('Deskripsi'),
                        ]),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->inline(false)
                            ->default(true),
                    ]),
            ]);
    }
}
