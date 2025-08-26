<?php

namespace App\Filament\Admin\Resources\AuditLogs\Pages;

use App\Filament\Admin\Resources\AuditLogs\AuditLogResource;
use App\Models\AuditLog;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Logs')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('audit-logs.export'))
                ->openUrlInNewTab(),
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Logs')
                ->badge(fn() => AuditLog::count()),

            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn() => AuditLog::whereDate('created_at', today())->count()),

            'created' => Tab::make('Created')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('action', 'created'))
                ->badge(fn() => AuditLog::where('action', 'created')->count())
                ->badgeColor('success'),

            'updated' => Tab::make('Updated')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('action', 'updated'))
                ->badge(fn() => AuditLog::where('action', 'updated')->count())
                ->badgeColor('warning'),

            'deleted' => Tab::make('Deleted')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('action', 'deleted'))
                ->badge(fn() => AuditLog::where('action', 'deleted')->count())
                ->badgeColor('danger'),
        ];
    }
}
