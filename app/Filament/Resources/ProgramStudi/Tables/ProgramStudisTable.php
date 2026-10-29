<?php

namespace App\Filament\Resources\ProgramStudi\Tables;

use App\Filament\Resources\ProgramStudi\ProgramStudiResource;
use App\Models\ProgramStudi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProgramStudisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Program Studi')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                BadgeColumn::make('jenjang')
                    ->label('Jenjang')
                    ->colors([
                        'info' => 'D3',
                        'success' => 'S1',
                        'warning' => 'S2',
                        'danger' => 'S3',
                    ])
                    ->sortable(),
                TextColumn::make('fakultas.nama')
                    ->label('Fakultas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordUrl(fn ($record): ?string => $record->trashed()
                ? null
                : ProgramStudiResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (ProgramStudi $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record): bool => $record->trashed()),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
