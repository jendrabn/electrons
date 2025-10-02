<?php

namespace App\Filament\Admin\Resources\PostSections;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;

class PostSectionSchema
{
    public static function getSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama Post Section')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(100)
                ->columnSpanFull(),
            Select::make('posts')
                ->label('Blog Posts')
                ->required()
                ->multiple()
                ->relationship(
                    name: 'posts',
                    titleAttribute: 'title',
                    modifyQueryUsing: fn ($query) => $query
                        ->where('posts.status', 'published')
                        ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                        ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                        ->with(['category', 'user'])
                        ->select('posts.*')
                        ->orderBy('posts.created_at', 'desc')
                )
                ->getOptionLabelFromRecordUsing(function ($record) {
                    $category = $record->category?->name ?? 'Tidak Ada Kategori';
                    $author = $record->user?->name ?? 'Tidak Ada Penulis';
                    $date = $record->created_at->format('d M Y');

                    return new HtmlString("
                                    <div class='py-2'>
                                        <div class='flex items-center justify-between mb-1'>
                                            <span class='font-semibold text-gray-900 dark:text-white'>
                                                #{$record->id} - {$record->title}
                                            </span>
                                        </div>
                                        <div class='text-xs text-gray-500 dark:text-gray-400 space-y-1'>
                                            <div>👤 Penulis: <span class='text-green-600 dark:text-green-400'>{$author}</span></div>
                                            <div>📂 Kategori: <span class='text-purple-600 dark:text-purple-400'>{$category}</span></div>
                                            <div>📅 Dibuat: <span class='text-blue-600 dark:text-blue-400'>{$date}</span></div>
                                        </div>
                                    </div>
                                ");
                })
                ->allowHtml()
                ->searchable(['posts.title', 'categories.name', 'users.name'])
                ->preload()
                ->placeholder('Pilih postingan untuk ditambahkan ke seksi')
                ->columnSpanFull(),

        ];
    }

    public static function mutateDataUsing(array $data): array
    {
        $data['slug'] = str()->slug($data['name']);

        return $data;
    }

    public static function getModalConfig(): array
    {
        return [
            'width' => Width::FiveExtraLarge,
            'alignment' => Alignment::Start,
        ];
    }
}
