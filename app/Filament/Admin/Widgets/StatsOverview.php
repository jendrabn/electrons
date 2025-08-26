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
            Stat::make('All Posts', Post::count())
                ->icon(Heroicon::Newspaper),
            Stat::make('Published Posts', Post::query()->published()->count())
                ->icon(Heroicon::Newspaper),
            Stat::make('Post Sections', PostSection::count())
                ->icon(Heroicon::Bars2),
            Stat::make('Categories', Category::count())
                ->icon(Heroicon::RectangleStack),
            Stat::make('Tags', Tag::count())
                ->icon(Heroicon::Tag),
            Stat::make('Admin', User::role(Role::ADMIN->value)->count())
                ->icon(Heroicon::Users),
            Stat::make('Author', User::role(Role::AUTHOR->value)->count())
                ->icon(Heroicon::Users),
        ];
    }
}
