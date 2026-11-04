<?php

namespace App\Filament\Resources\ProgramStudi\Schemas;

use App\Models\JenjangPendidikan;
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
                    ->options(fn (callable $get) => JenjangPendidikan::getOptionsIncludingInactive(
                        $get('jenjang') ? [$get('jenjang')] : []
                    )->pluck('nama', 'kode')->toArray())
                    ->required()
                    ->searchable()
                    ->native(false)
                    ->getOptionLabelUsing(function (string $value): string {
                        $jenjang = JenjangPendidikan::where('kode', $value)->first();
                        if ($jenjang && ! $jenjang->is_active) {
                            return $jenjang->nama.' (Non-Aktif)';
                        }

                        return $jenjang->nama ?? $value;
                    }),
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
