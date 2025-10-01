<?php

namespace App\Filament\Admin\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Audit')
                    ->description('Informasi dasar tentang entri log audit ini')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID Audit')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),

                                TextEntry::make('auditable_type')
                                    ->label('Tipe Model')
                                    ->formatStateUsing(fn ($state) => class_basename($state))
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('auditable_id')
                                    ->label('ID Rekaman')
                                    ->copyable()
                                    ->icon('heroicon-o-key'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('action')
                                    ->badge()
                                    ->size('lg')
                                    ->weight(FontWeight::Bold)
                                    ->color(fn (string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'info',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'created' => 'Dibuat',
                                        'updated' => 'Diperbarui',
                                        'deleted' => 'Dihapus',
                                        default => ucfirst($state),
                                    }),

                                TextEntry::make('created_at')
                                    ->label('Tanggal & Waktu')
                                    ->dateTime('l, d F Y \a\t H:i:s')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ]),

                Section::make('Deskripsi')
                    ->description('Penjelasan singkat mengenai apa yang terjadi')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('description')
                            ->prose()
                            ->size('lg'),
                    ]),

                Section::make('Informasi Pengguna')
                    ->description('Informasi tentang siapa yang melakukan aksi ini')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('USERNAME')
                                    ->default('Pengguna Sistem')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('user.email')
                                    ->label('Email Pengguna')
                                    ->default('system@app.local')
                                    ->icon('heroicon-o-envelope'),

                                TextEntry::make('ip_address')
                                    ->label('Alamat IP')
                                    ->copyable()
                                    ->icon('heroicon-o-globe-alt'),
                            ]),
                    ]),

                Section::make('Perubahan Data')
                    ->description('Nilai sebelum dan sesudah dari data yang berubah')
                    ->icon('heroicon-o-arrow-path')
                    ->collapsible()
                    ->columnSpanFull()
                    ->visible(fn ($record) => ! empty($record->old_values) || ! empty($record->new_values))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValueEntry::make('old_values')
                                    ->label('Nilai Sebelum')
                                    ->visible(fn ($record) => ! empty($record->old_values))
                                    ->columnSpanFull(fn ($record) => empty($record->new_values))
                                    ->getStateUsing(function ($record) {
                                        return collect($record->old_values)
                                            ->map(fn ($value) => strip_tags($value))
                                            ->toArray();
                                    }),

                                KeyValueEntry::make('new_values')
                                    ->label('Nilai Sesudah')
                                    ->visible(fn ($record) => ! empty($record->new_values))
                                    ->columnSpanFull(fn ($record) => empty($record->old_values))
                                    ->getStateUsing(function ($record) {
                                        return collect($record->old_values)
                                            ->map(fn ($value) => strip_tags($value))
                                            ->toArray();
                                    }),
                            ]),
                    ]),

                Section::make('Ringkasan Perubahan')
                    ->description('Ringkasan bidang yang berubah')
                    ->icon('heroicon-o-list-bullet')
                    ->collapsible()
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->action === 'updated' && ! empty($record->getChanges()))
                    ->schema([
                        TextEntry::make('changes_summary')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $changes = $record->getChanges();

                                if (empty($changes)) {
                                    return 'Tidak ada perubahan bidang yang terdeteksi.';
                                }

                                $summary = [];
                                foreach ($changes as $field => $change) {
                                    $oldValue = strip_tags($record->formatValue($change['old'] ?? ''));
                                    $newValue = strip_tags($record->formatValue($change['new'] ?? ''));

                                    $fieldName = ucwords(str_replace('_', ' ', $field));
                                    $summary[] = "**{$fieldName}**\n";
                                    $summary[] = "- Sebelum: `{$oldValue}`";
                                    $summary[] = "- Sesudah: `{$newValue}`";
                                    $summary[] = ''; // Empty line for spacing
                                }

                                return implode("\n", $summary);
                            })
                            ->prose()
                            ->markdown(),
                    ]),
                Section::make('Detail Teknis')
                    ->description('Informasi teknis tambahan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('user_agent')
                                    ->label('Agen Pengguna')
                                    ->prose()
                                    ->copyable(),

                                TextEntry::make('updated_at')
                                    ->label('Log Diperbarui Pada')
                                    ->dateTime()
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ]),
            ]);
    }
}
