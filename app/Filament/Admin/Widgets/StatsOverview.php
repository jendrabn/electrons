<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('All Posts', Post::count()),
            Stat::make('Published Posts', Post::query()->published()->count())
                ->description('Last 30 days'),
            Stat::make('Admin', User::role(Role::ADMIN->value)->count()),
            Stat::make('Author', User::role(Role::AUTHOR->value)->count()),
        ];
    }
}
