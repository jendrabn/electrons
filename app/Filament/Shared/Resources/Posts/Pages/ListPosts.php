<?php

namespace App\Filament\Shared\Resources\Posts\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Blog Post'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(function () {
                    if (auth()->user()->isAdmin()) {
                        return Post::count();
                    }

                    if (auth()->user()->isAuthor()) {
                        return Post::where('user_id', auth()->id())->count();
                    }

                    return 0;
                }),
            'draft' => Tab::make('Draf')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Status::DRAFT->value))
                ->badge(function () {
                    if (auth()->user()->isAdmin()) {
                        return Post::where('status', Status::DRAFT->value)->count();
                    }

                    if (auth()->user()->isAuthor()) {
                        return Post::where('user_id', auth()->id())->where('status', Status::DRAFT->value)->count();
                    }

                    return 0;
                }),
            'pending' => Tab::make('Tertunda')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Status::PENDING->value))
                ->badge(function () {
                    if (auth()->user()->isAdmin()) {
                        return Post::where('status', Status::PENDING->value)->count();
                    }

                    if (auth()->user()->isAuthor()) {
                        return Post::where('user_id', auth()->id())->where('status', Status::PENDING->value)->count();
                    }

                    return 0;
                }),
            'published' => Tab::make('Terbit')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Status::PUBLISHED))
                ->badge(function () {
                    if (auth()->user()->isAdmin()) {
                        return Post::where('status', Status::PUBLISHED)->count();
                    }

                    if (auth()->user()->isAuthor()) {
                        return Post::where('user_id', auth()->id())->where('status', Status::PUBLISHED)->count();
                    }

                    return 0;
                }),
            'rejected' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Status::REJECTED))
                ->badge(function () {
                    if (auth()->user()->isAdmin()) {
                        return Post::where('status', Status::REJECTED)->count();
                    }

                    if (auth()->user()->isAuthor()) {
                        return Post::where('user_id', auth()->id())->where('status', Status::REJECTED)->count();
                    }

                    return 0;
                }),
            'archived' => Tab::make('Arsip')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', Status::ARCHIVED))
                ->badge(function () {
                    if (auth()->user()->isAdmin()) {
                        return Post::where('status', Status::ARCHIVED)->count();
                    }

                    if (auth()->user()->isAuthor()) {
                        return Post::where('user_id', auth()->id())->where('status', Status::ARCHIVED)->count();
                    }

                    return 0;
                }),

        ];
    }
}
