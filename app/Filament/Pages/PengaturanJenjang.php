<?php

namespace App\Filament\Pages;

use App\Models\JenjangPendidikan;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class PengaturanJenjang extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Pengaturan Jenjang';

    protected static ?string $title = 'Pengaturan Jenjang Pendidikan';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.pengaturan-jenjang';

    public function table(Table $table): Table
    {
        return $table
            ->query(JenjangPendidikan::query()->orderBy('urutan')->orderBy('nama'))
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable(),

                TextColumn::make('nama')
                    ->label('Nama Jenjang')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                BadgeColumn::make('kelompok')
                    ->label('Kelompok')
                    ->colors([
                        'success' => 'Dasar',
                        'warning' => 'Menengah',
                        'primary' => 'Tinggi',
                    ])
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Status')
                    ->sortable()
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('urutan')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('urutan', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
