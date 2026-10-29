<?php

namespace App\Filament\Resources\KategoriMinat\Tables;

use App\Models\KategoriMinat;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KategoriMinatTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (KategoriMinat $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (KategoriMinat $record): bool => $record->trashed()),
                DeleteAction::make()
                    ->disabled(fn (KategoriMinat $record): bool => $record->userMinat()->exists()
                        || $record->lowonganMinat()->exists()
                        || $record->pelatihanMinat()->exists()
                    )
                    ->tooltip(fn (KategoriMinat $record): ?string => ($record->userMinat()->exists()
                        || $record->lowonganMinat()->exists()
                        || $record->pelatihanMinat()->exists())
                        ? 'Tidak bisa dihapus karena sedang digunakan'
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
