<?php

declare(strict_types=1);

namespace App\Filament\Resources\Role;

use App\Filament\Resources\Role\Pages\CreateRole;
use App\Filament\Resources\Role\Pages\EditRole;
use App\Filament\Resources\Role\Pages\ListRoles;
use App\Filament\Resources\Role\Pages\ViewRole;
use App\Filament\Resources\Role\Schemas\RoleForm;
use App\Filament\Resources\Role\Tables\RoleTable;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use BezhanSalleh\PluginEssentials\Concerns\Resource as Essentials;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class RoleResource extends Resource
{
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasGlobalSearch;
    use Essentials\HasLabels;
    use Essentials\HasNavigation;
    use HasShieldFormComponents;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Pengaturan';
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return RoleTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    #[Override]
    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    /**
     * Slug tetap diambil dari config Shield (`shield_resource.slug` = `shield/roles`)
     * agar URL `admin/shield/roles` tidak berubah setelah Resource dipindah ke
     * folder `Role/`. Properti statis `$slug` tidak dipakai di sini karena method
     * ini meng-override `Resource::getSlug()`.
     */
    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster();
    }

    public static function getEssentialsPlugin(): ?FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }
}
