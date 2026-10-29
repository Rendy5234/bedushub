<?php

namespace App\Filament\Resources\Institusi;

use App\Filament\Resources\Institusi\Pages\CreateInstitusi;
use App\Filament\Resources\Institusi\Pages\EditInstitusi;
use App\Filament\Resources\Institusi\Pages\ListInstitusi;
use App\Filament\Resources\Institusi\Pages\ViewInstitusi;
use App\Filament\Resources\Institusi\Schemas\InstitusiForm;
use App\Filament\Resources\Institusi\Tables\InstitusiTable;
use App\Models\Institusi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InstitusiResource extends Resource
{
    protected static ?int $navigationSort = 1;

    protected static ?string $model = Institusi::class;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $modelLabel = 'Institusi';

    protected static ?string $pluralModelLabel = 'Institusi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Institusi';

    protected static ?string $slug = 'institusi';

    public static function form(Schema $schema): Schema
    {
        return InstitusiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstitusiTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstitusi::route('/'),
            'create' => CreateInstitusi::route('/create'),
            'edit' => EditInstitusi::route('/{record}/edit'),
            'view' => ViewInstitusi::route('/{record}'),
        ];
    }
}
