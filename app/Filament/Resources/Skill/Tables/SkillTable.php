<?php

namespace App\Filament\Resources\Skill\Tables;

use App\Models\Skill;
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

class SkillTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Skill')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('kategori')
                    ->label('Kategori')
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
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (Skill $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (Skill $record): bool => $record->trashed()),
                DeleteAction::make()
                    ->disabled(fn (Skill $record): bool => $record->userSkills()->exists()
                        || $record->lowonganSkills()->exists()
                        || $record->pelatihanSkills()->exists()
                    )
                    ->tooltip(fn (Skill $record): ?string => ($record->userSkills()->exists()
                        || $record->lowonganSkills()->exists()
                        || $record->pelatihanSkills()->exists())
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
