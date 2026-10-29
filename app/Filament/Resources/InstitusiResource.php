<?php

namespace App\Filament\Resources;

use App\Enums\JenisInstitusi;
use App\Filament\Resources\InstitusiResource\Pages;
use App\Models\Institusi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InstitusiResource extends Resource
{
    protected static ?int $navigationSort = 1;

    protected static ?string $model = Institusi::class;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $modelLabel = 'Institusi';

    protected static ?string $pluralModelLabel = 'Institusi';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Institusi';

    protected static ?string $slug = 'institusi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Institusi')
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama')
                                ->label('Nama Institusi')
                                ->required()
                                ->maxLength(255),
                            Select::make('jenis')
                                ->label('Jenis Institusi')
                                ->options(JenisInstitusi::class)
                                ->required()
                                ->native(false),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('kecamatan')
                                ->label('Kecamatan')
                                ->maxLength(255),
                            TextInput::make('alamat')
                                ->label('Alamat')
                                ->maxLength(255),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitusi::route('/'),
            'create' => Pages\CreateInstitusi::route('/create'),
            'edit' => Pages\EditInstitusi::route('/{record}/edit'),
            'view' => Pages\ViewInstitusi::route('/{record}'),
        ];
    }
}
