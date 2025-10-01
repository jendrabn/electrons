<?php

namespace App\Filament\Shared\Resources\Posts;

use App\Enums\Status;
use App\Filament\Shared\Resources\Posts\Pages\CreatePost;
use App\Filament\Shared\Resources\Posts\Pages\EditPost;
use App\Filament\Shared\Resources\Posts\Pages\ListPosts;
use App\Filament\Shared\Resources\Posts\Pages\ViewPost;
use App\Filament\Shared\Resources\Posts\Schemas\PostForm;
use App\Filament\Shared\Resources\Posts\Schemas\PostInfolist;
use App\Filament\Shared\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Blog Post';

    protected static ?string $pluralModelLabel = 'Blog Post';

    protected static ?string $navigationLabel = 'Blog Post';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view' => ViewPost::route('/{record}'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }

    public static function maxContentWidth(): string
    {
        return 'full';
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->isAdmin()) {
            return static::getModel()::where('status', Status::PENDING->value)->count();
        }

        return null;
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->isAdmin()) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
