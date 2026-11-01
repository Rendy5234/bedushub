<?php

namespace App\Filament\Resources\Skill;

use App\Filament\Resources\Skill\Pages\CreateSkill;
use App\Filament\Resources\Skill\Pages\EditSkill;
use App\Filament\Resources\Skill\Pages\ListSkills;
use App\Filament\Resources\Skill\Pages\ViewSkill;
use App\Filament\Resources\Skill\Schemas\SkillForm;
use App\Filament\Resources\Skill\Tables\SkillTable;
use App\Models\Skill;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $modelLabel = 'Skill';

    protected static ?string $pluralModelLabel = 'Skills';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Skills';

    protected static ?string $slug = 'skills';

    public static function form(Schema $schema): Schema
    {
        return SkillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SkillTable::configure($table);
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
            'index' => ListSkills::route('/'),
            'create' => CreateSkill::route('/create'),
            'edit' => EditSkill::route('/{record}/edit'),
            'view' => ViewSkill::route('/{record}'),
        ];
    }
}
