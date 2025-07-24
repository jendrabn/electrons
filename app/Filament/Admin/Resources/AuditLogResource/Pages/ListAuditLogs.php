<?php

// app/Filament/Resources/AuditLogResource/Pages/ListAuditLogs.php
namespace App\Filament\Admin\Resources\AuditLogResource\Pages;

use App\Filament\Admin\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Logs')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('audit-logs.export'))
                ->openUrlInNewTab(),

            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->redirect(request()->url())),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Logs')
                ->badge(fn() => \App\Models\AuditLog::count()),

            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn() => \App\Models\AuditLog::whereDate('created_at', today())->count()),

            'created' => Tab::make('Created')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('action', 'created'))
                ->badge(fn() => \App\Models\AuditLog::where('action', 'created')->count())
                ->badgeColor('success'),

            'updated' => Tab::make('Updated')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('action', 'updated'))
                ->badge(fn() => \App\Models\AuditLog::where('action', 'updated')->count())
                ->badgeColor('warning'),

            'deleted' => Tab::make('Deleted')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('action', 'deleted'))
                ->badge(fn() => \App\Models\AuditLog::where('action', 'deleted')->count())
                ->badgeColor('danger'),
        ];
    }
}
