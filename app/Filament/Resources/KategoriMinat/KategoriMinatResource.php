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
    protected static ?string $model = KategoriMinat::class;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    /**
     * Slug eksplisit supaya URL dan nama route tetap `admin/kategori-minats`
     * meskipun Resource berada di dalam folder `KategoriMinat/`.
     */
    protected static ?string $slug = 'kategori-minats';

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
