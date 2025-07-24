<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * @see https://filamentphp.com/docs/3.x/panels/dashboard#customizing-the-dashboard-page
     */

    protected static string $routePath = 'dashboard';

    protected static ?string $title = 'Dashboard';
}
