<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriMinatResource\Pages;
use App\Models\KategoriMinat;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KategoriMinatResource extends Resource
{
    protected static ?string $model = KategoriMinat::class;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori Minat')
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama')
                                ->label('Nama Kategori Minat')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('deskripsi')
                                ->label('Deskripsi'),
                        ]),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->inline(false)
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriMinats::route('/'),
            'create' => Pages\CreateKategoriMinat::route('/create'),
            'edit' => Pages\EditKategoriMinat::route('/{record}/edit'),
            'view' => Pages\ViewKategoriMinat::route('/{record}'),
        ];
    }
}
