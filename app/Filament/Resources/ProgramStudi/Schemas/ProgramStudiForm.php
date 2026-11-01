<?php

namespace App\Filament\Resources\ProgramStudi\Schemas;

use App\Models\ProgramStudi;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ProgramStudiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Program Studi')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        ProgramStudi::class,
                        'nama',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('fakultas_id', $get('fakultas_id')),
                    ),
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
