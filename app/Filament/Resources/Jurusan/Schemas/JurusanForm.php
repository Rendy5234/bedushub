<?php

namespace App\Filament\Resources\Jurusan\Schemas;

use App\Enums\JenisInstitusi;
use App\Models\Jurusan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class JurusanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('institusi_id')
                    ->label('Institusi')
                    ->relationship('institusi', 'nama', fn ($query) => $query->whereIn('jenis', [JenisInstitusi::Smk->value, JenisInstitusi::Sma->value]))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),
                TextInput::make('nama')
                    ->label('Nama Jurusan')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        Jurusan::class,
                        'nama',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('institusi_id', $get('institusi_id')),
                    ),
                TextInput::make('kode')
                    ->label('Kode')
                    ->maxLength(20)
                    ->placeholder('Contoh: TKJ'),
            ]);
    }
}
