<?php

namespace App\Filament\Resources\Skill\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Skill')
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama')
                                ->label('Nama Skill')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('kategori')
                                ->label('Kategori')
                                ->placeholder('Contoh: Programming, Design, Marketing')
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
