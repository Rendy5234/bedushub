<?php

namespace App\Filament\Resources\Fakultas\Schemas;

use App\Models\Fakultas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class FakultasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Fakultas')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        Fakultas::class,
                        'nama',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('institusi_id', $get('institusi_id')),
                    ),
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
