<?php

namespace App\Filament\Admin\Resources\PostSections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PostSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Section Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)
                    ->columnSpanFull(),

                Select::make('posts')
                    ->label('Posts')
                    ->required()
                    ->multiple()
                    ->relationship(
                        name: 'posts',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn($query) => $query
                            ->where('posts.status', 'published')
                            ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                            ->with(['category', 'user'])
                            ->select('posts.*')
                            ->orderBy('posts.created_at', 'desc')
                    )
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $category = $record->category?->name ?? 'No Category';
                        $author = $record->user?->name ?? 'No Author';
                        $date = $record->created_at->format('d M Y');

                        return new HtmlString("
                            <div class='py-2'>
                                <div class='flex items-center justify-between mb-1'>
                                    <span class='font-semibold text-gray-900 dark:text-white'>
                                        #{$record->id} - {$record->title}
                                    </span>
                                </div>
                                <div class='text-xs text-gray-500 dark:text-gray-400 space-y-1'>
                                    <div>👤 Author: <span class='text-green-600 dark:text-green-400'>{$author}</span></div>
                                    <div>📂 Category: <span class='text-purple-600 dark:text-purple-400'>{$category}</span></div>
                                    <div>📅 Created: <span class='text-blue-600 dark:text-blue-400'>{$date}</span></div>
                                </div>
                            </div>
                        ");
                    })
                    ->allowHtml()
                    ->searchable(['posts.title', 'categories.name', 'users.name'])
                    ->preload()
                    ->placeholder('Select posts to add to section')
                    ->columnSpanFull(),
            ]);
    }
}
