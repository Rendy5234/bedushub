<?php

namespace App\Filament\Resources\Fakultas;

use App\Filament\Resources\Fakultas\Pages\CreateFakultas;
use App\Filament\Resources\Fakultas\Pages\EditFakultas;
use App\Filament\Resources\Fakultas\Pages\ListFakultas;
use App\Filament\Resources\Fakultas\Schemas\FakultasForm;
use App\Filament\Resources\Fakultas\Tables\FakultasTable;
use App\Models\Fakultas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class FakultasResource extends Resource
{
    protected static ?string $model = Fakultas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $modelLabel = 'Fakultas';

    protected static ?string $pluralModelLabel = 'Fakultas';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Fakultas';

    protected static ?string $slug = 'fakultas';

    public static function form(Schema $schema): Schema
    {
        return FakultasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FakultasTable::configure($table);
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
            'index' => ListFakultas::route('/'),
            'create' => CreateFakultas::route('/create'),
            'edit' => EditFakultas::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
