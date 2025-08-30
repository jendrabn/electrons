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
                ->label('Add Post'),
        ];
    }

    public function getTabs(): array
    {
        $status = Status::class;

        return [
            'all' => Tab::make('All')
                ->badge(fn() => Post::count()),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', $status::DRAFT))
                ->badge(fn() => Post::where('status', $status::DRAFT)->count()),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', $status::PENDING))
                ->badge(fn() => Post::where('status', $status::PENDING)->count()),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', $status::PUBLISHED))
                ->badge(fn() => Post::where('status', $status::PUBLISHED)->count()),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', $status::REJECTED))
                ->badge(fn() => Post::where('status', $status::REJECTED)->count()),
            'archived' => Tab::make('Archived')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', $status::ARCHIVED))
                ->badge(fn() => Post::where('status', $status::ARCHIVED)->count()),

        ];
    }
}
