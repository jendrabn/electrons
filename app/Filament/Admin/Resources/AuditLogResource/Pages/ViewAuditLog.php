<?php

// app/Filament/Resources/AuditLogResource/Pages/ViewAuditLog.php
namespace App\Filament\Admin\Resources\AuditLogResource\Pages;

use App\Filament\Admin\Resources\AuditLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Actions;
use Filament\Support\Enums\FontWeight;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),

            Actions\Action::make('view_record')
                ->label('View Original Record')
                ->icon('heroicon-o-eye')
                ->visible(fn() => $this->record->auditable)
                ->url(function () {
                    $record = $this->record;
                    $model = $record->auditable;
                    if (!$model) return null;

                    // Try to generate URL based on model type
                    $modelClass = class_basename($record->auditable_type);
                    $resourceClass = "App\\Filament\\Resources\\{$modelClass}Resource";

                    if (class_exists($resourceClass)) {
                        return $resourceClass::getUrl('edit', ['record' => $model]);
                    }

                    return null;
                })
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Audit Information')
                    ->description('Basic information about this audit log entry')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Audit ID')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                TextEntry::make('auditable_type')
                                    ->label('Model Type')
                                    ->formatStateUsing(fn($state) => class_basename($state))
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('auditable_id')
                                    ->label('Record ID')
                                    ->copyable()
                                    ->icon('heroicon-o-key'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('action')
                                    ->badge()
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->color(fn(string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'info',
                                    }),

                                TextEntry::make('created_at')
                                    ->label('Date & Time')
                                    ->dateTime('l, d F Y \a\t H:i:s')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ]),

                Section::make('Description')
                    ->description('Human-readable description of what happened')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('description')
                            ->prose()
                            ->size(TextEntry\TextEntrySize::Large),
                    ]),

                Section::make('User Information')
                    ->description('Information about who performed this action')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User Name')
                                    ->default('System User')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('user.email')
                                    ->label('User Email')
                                    ->default('system@app.local')
                                    ->icon('heroicon-o-envelope'),

                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->copyable()
                                    ->icon('heroicon-o-globe-alt'),
                            ]),
                    ]),

                Section::make('Data Changes')
                    ->description('Before and after values of the changed data')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn() => !empty($this->record->old_values) || !empty($this->record->new_values))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValueEntry::make('old_values')
                                    ->label('Previous Values')
                                    ->visible(fn() => !empty($this->record->old_values))
                                    ->columnSpanFull(fn() => empty($this->record->new_values))
                                    ->getStateUsing(function () {
                                        return collect($this->record->old_values)
                                            ->map(fn($value) => strip_tags($value))
                                            ->toArray();
                                    }),

                                KeyValueEntry::make('new_values')
                                    ->label('New Values')
                                    ->visible(fn() => !empty($this->record->new_values))
                                    ->columnSpanFull(fn() => empty($this->record->old_values))->getStateUsing(function () {
                                        return collect($this->record->old_values)
                                            ->map(fn($value) => strip_tags($value))
                                            ->toArray();
                                    }),
                            ]),
                    ]),

                Section::make('Changes Summary')
                    ->description('Summary of what fields were changed')
                    ->icon('heroicon-o-list-bullet')
                    ->visible(fn() => $this->record->action === 'updated' && !empty($this->record->getChanges()))
                    ->schema([
                        TextEntry::make('changes_summary')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $changes = $record->getChanges();

                                if (empty($changes)) {
                                    return 'No specific field changes detected.';
                                }

                                $summary = [];
                                foreach ($changes as $field => $change) {
                                    $oldValue = strip_tags($record->formatValue($change['old'] ?? ''));
                                    $newValue = strip_tags($record->formatValue($change['new'] ?? ''));

                                    $fieldName = ucwords(str_replace('_', ' ', $field));
                                    $summary[] = "**{$fieldName}**\n";
                                    $summary[] = "- Before: `{$oldValue}`";
                                    $summary[] = "- After: `{$newValue}`";
                                    $summary[] = ""; // Empty line for spacing
                                }

                                return implode("\n", $summary);
                            })
                            ->prose()
                            ->markdown(),
                    ]),
                Section::make('Technical Details')
                    ->description('Additional technical information')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('user_agent')
                                    ->label('User Agent')
                                    ->prose()
                                    ->copyable(),

                                TextEntry::make('updated_at')
                                    ->label('Log Updated At')
                                    ->dateTime()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ]),
            ]);
    }
}
