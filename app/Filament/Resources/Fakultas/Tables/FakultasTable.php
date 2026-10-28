<?php

namespace App\Filament\Resources\Fakultas\Tables;

use App\Filament\Resources\Fakultas\FakultasResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FakultasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Fakultas')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('institusi.nama')
                    ->label('Institusi')
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
                : FakultasResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record): bool => $record->trashed()),
                DeleteAction::make()
                    ->disabled(fn ($record): bool => $record->hasProgramStudi())
                    ->tooltip(fn ($record): ?string => $record->hasProgramStudi()
                        ? 'Tidak bisa dihapus karena masih memiliki Program Studi'
                        : null),
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
