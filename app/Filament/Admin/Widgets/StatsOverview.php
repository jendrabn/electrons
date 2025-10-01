<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostSection;
use App\Models\Tag;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Semua Blog Post', Post::count())
                ->icon(Heroicon::Newspaper),
            Stat::make('Blog Post Terbit', Post::query()->published()->count())
                ->icon(Heroicon::Newspaper),
            Stat::make('Post Section', PostSection::count())
                ->icon(Heroicon::Bars2),
            Stat::make('Kategori', Category::count())
                ->icon(Heroicon::RectangleStack),
            Stat::make('Tag', Tag::count())
                ->icon(Heroicon::Tag),
            Stat::make('Admin', User::role(Role::ADMIN->value)->count())
                ->icon(Heroicon::Users),
            Stat::make('Penulis', User::role(Role::AUTHOR->value)->count())
                ->icon(Heroicon::Users),
        ];
    }
}
