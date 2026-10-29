<?php

declare(strict_types=1);

namespace App\Filament\Resources\Role\Tables;

use App\Filament\Resources\Role\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight(FontWeight::Medium)
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning')
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                TextColumn::make('team.name')
                    ->default('Global')
                    ->badge()
                    ->color(fn (mixed $state): string => str($state)->contains('Global') ? 'gray' : 'primary')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn (): bool => RoleResource::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->color('primary'),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(),
            ])
            ->recordUrl(
                fn (Role $record): ?string => $record->name === 'super_admin'
                    ? null
                    : RoleResource::getUrl('edit', ['record' => $record])
            )
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn (Role $record): bool => $record->name === 'super_admin')
                    ->tooltip(fn (Role $record): ?string => $record->name === 'super_admin'
                            ? 'Role Super Admin tidak boleh diubah'
                            : null
                    ),
                DeleteAction::make()
                    ->disabled(fn (Role $record): bool => $record->name === 'super_admin'
                        || $record->users()->exists()
                    )
                    ->tooltip(fn (Role $record): ?string => ($record->name === 'super_admin' || $record->users()->exists())
                            ? 'Role ini tidak bisa dihapus karena Super Admin atau sedang digunakan'
                            : null
                    ),
            ]);
    }
}
