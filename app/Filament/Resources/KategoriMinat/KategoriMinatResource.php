<?php

namespace App\Filament\Resources\KategoriMinat;

use App\Filament\Resources\KategoriMinat\Pages\CreateKategoriMinat;
use App\Filament\Resources\KategoriMinat\Pages\EditKategoriMinat;
use App\Filament\Resources\KategoriMinat\Pages\ListKategoriMinats;
use App\Filament\Resources\KategoriMinat\Pages\ViewKategoriMinat;
use App\Filament\Resources\KategoriMinat\Schemas\KategoriMinatForm;
use App\Filament\Resources\KategoriMinat\Tables\KategoriMinatTable;
use App\Models\KategoriMinat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KategoriMinatResource extends Resource
{
    protected static ?int $navigationSort = 4;
    
    protected static ?string $model = KategoriMinat::class;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $modelLabel = 'Kategori Minat';

    protected static ?string $pluralModelLabel = 'Kategori Minat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Kategori Minat';

    protected static ?string $slug = 'kategori-minat';

    public static function form(Schema $schema): Schema
    {
        return KategoriMinatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriMinatTable::configure($table);
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
            'index' => ListKategoriMinats::route('/'),
            'create' => CreateKategoriMinat::route('/create'),
            'edit' => EditKategoriMinat::route('/{record}/edit'),
            'view' => ViewKategoriMinat::route('/{record}'),
        ];
    }
}
