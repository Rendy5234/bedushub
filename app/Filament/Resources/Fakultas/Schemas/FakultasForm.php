<?php

namespace App\Filament\Resources\Fakultas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FakultasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Fakultas')
                    ->required()
                    ->maxLength(255),
                Select::make('institusi_id')
                    ->label('Institusi')
                    ->relationship('institusi', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),
            ]);
    }
}
