<?php

namespace App\Filament\Resources\Institusi\Schemas;

use App\Enums\JenisInstitusi;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InstitusiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Institusi')
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama')
                                ->label('Nama Institusi')
                                ->required()
                                ->maxLength(255),
                            Select::make('jenis')
                                ->label('Jenis Institusi')
                                ->options(JenisInstitusi::class)
                                ->required()
                                ->native(false),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('kecamatan')
                                ->label('Kecamatan')
                                ->maxLength(255),
                            TextInput::make('alamat')
                                ->label('Alamat')
                                ->maxLength(255),
                        ]),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->inline(false)
                            ->default(true),
                    ]),
            ]);
    }
}
