<?php

namespace App\Filament\Resources\Institusi\Tables;

use App\Enums\JenisInstitusi;
use App\Filament\Resources\Institusi\InstitusiResource;
use App\Models\Institusi;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InstitusiTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Institusi')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (JenisInstitusi $state): string => $state->getLabel())
                    ->color(fn (JenisInstitusi $state): string => match ($state) {
                        JenisInstitusi::Smk => 'info',
                        JenisInstitusi::Sma => 'success',
                        JenisInstitusi::Pt => 'warning',
                    }),
                TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable()
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
                SelectFilter::make('jenis')
                    ->label('Jenis Institusi')
                    ->options(JenisInstitusi::class)
                    ->native(false),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                TrashedFilter::make(),
            ])
            ->recordUrl(fn (Institusi $record): ?string => $record->trashed()
                ? null
                : InstitusiResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (Institusi $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (Institusi $record): bool => $record->trashed()),
                DeleteAction::make()
                    ->disabled(fn (Institusi $record): bool => $record->hasFakultasOrJurusan())
                    ->tooltip(fn (Institusi $record): ?string => $record->hasFakultasOrJurusan()
                        ? 'Tidak bisa dihapus karena masih memiliki Fakultas atau Jurusan'
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
