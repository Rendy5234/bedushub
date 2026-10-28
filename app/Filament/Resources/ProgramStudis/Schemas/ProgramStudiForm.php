<?php

namespace App\Filament\Resources\ProgramStudis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProgramStudiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Program Studi')
                    ->required()
                    ->maxLength(255),
                TextInput::make('kode')
                    ->label('Kode')
                    ->maxLength(20)
                    ->placeholder('Contoh: IF001'),
                Select::make('jenjang')
                    ->label('Jenjang')
                    ->options([
                        'D3' => 'D3',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->required()
                    ->searchable()
                    ->native(false),
                Select::make('fakultas_id')
                    ->label('Fakultas')
                    ->relationship('fakultas', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),
            ]);
    }
}
