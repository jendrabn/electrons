<?php

namespace App\Filament\Admin\Resources\PostSections;

use App\Filament\Admin\Resources\PostSections\Pages\CreatePostSection;
use App\Filament\Admin\Resources\PostSections\Pages\EditPostSection;
use App\Filament\Admin\Resources\PostSections\Pages\ListPostSections;
use App\Filament\Admin\Resources\PostSections\Schemas\PostSectionForm;
use App\Filament\Admin\Resources\PostSections\Tables\PostSectionsTable;
use App\Models\PostSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PostSectionResource extends Resource
{
    protected static ?string $model = PostSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Post Section';

    public static function form(Schema $schema): Schema
    {
        return PostSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostSectionsTable::configure($table);
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
            'index' => ListPostSections::route('/'),
            'create' => CreatePostSection::route('/create'),
            'edit' => EditPostSection::route('/{record}/edit'),
        ];
    }
}
