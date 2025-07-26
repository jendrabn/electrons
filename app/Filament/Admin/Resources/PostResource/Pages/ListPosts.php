<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Post')
        ];
    }


    public function getTabs(): array
    {
        $tabs = [];

        foreach (Status::cases() as $status) {
            $tabs[$status->value] = Tab::make()
                ->label($status->getLabel()) // gunakan label custom kalau ada
                ->badge(fn() => \App\Models\Post::where('status', $status->value)->count())
                ->modifyQueryUsing(fn($query) => $query->where('status', $status->value));
        }

        return [
            'all' => Tab::make('All')
                ->badge(fn() => \App\Models\Post::count()),
            ...$tabs,
        ];
    }


    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}
