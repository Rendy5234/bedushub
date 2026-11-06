<?php

namespace App\Filament\Resources\Institusi\Schemas;

use App\Models\Institusi;
use App\Models\JenjangPendidikan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                                ->maxLength(255)
                                ->unique(Institusi::class, 'nama', ignoreRecord: true),
                            Select::make('jenis')
                                ->label('Jenis Institusi')
                                ->options(fn (Get $get): array => JenjangPendidikan::getInstitusiJenisOptions($get('jenis')))
                                ->getOptionLabelUsing(fn (string $value): string => $value)
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
