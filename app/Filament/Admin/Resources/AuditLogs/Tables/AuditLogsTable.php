<?php

namespace App\Filament\Admin\Resources\AuditLogs\Tables;

use App\Models\AuditLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->size('sm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('auditable_type')
                    ->label('MODEL')
                    ->size('sm')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('auditable_id')
                    ->label('ID REKAMAN')
                    ->size('sm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('action')
                    ->label('AKSI')
                    ->size('sm')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'info',
                    })
                    ->sortable(),
                TextColumn::make('description')
                    ->label('DESKRIPSI')
                    ->size('sm')
                    ->limit(60)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 60 ? $state : null;
                    })
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('PENGGUNA')
                    ->size('sm')
                    ->default('Sistem')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('ALAMAT IP')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('TANGGAL & WAKTU')
                    ->size('sm')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Aksi')
                    ->options([
                        'created' => 'Dibuat',
                        'updated' => 'Diperbarui',
                        'deleted' => 'Dihapus',
                    ])
                    ->multiple()
                    ->placeholder('Semua Aksi'),
                SelectFilter::make('auditable_type')
                    ->label('Tipe Model')
                    ->options(function () {
                        return AuditLog::select('auditable_type')
                            ->distinct()
                            ->pluck('auditable_type')
                            ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                            ->sort()
                            ->toArray();
                    })
                    ->multiple()
                    ->placeholder('Semua Model'),
                SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->multiple()
                    ->placeholder('Semua Pengguna'),
                Filter::make('created_at')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Tanggal Mulai')
                            ->placeholder('Pilih tanggal mulai'),
                        DatePicker::make('created_until')
                            ->label('Tanggal Selesai')
                            ->placeholder('Pilih tanggal selesai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Dari: '.$data['created_from'];
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Sampai: '.$data['created_until'];
                        }

                        return $indicators;
                    }),

                Filter::make('has_changes')
                    ->label('Memiliki Perubahan Data')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->whereNotNull('old_values')->orWhereNotNull('new_values');
                    }))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('30s') // Auto refresh setiap 30 detik
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_audit_logs');
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->description;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Model' => class_basename($record->auditable_type),
            'Aksi' => match ($record->action) {
                'created' => 'Dibuat',
                'updated' => 'Diperbarui',
                'deleted' => 'Dihapus',
                default => ucfirst($record->action),
            },
            'Pengguna' => $record->user?->name ?? 'Sistem',
            'Tanggal' => $record->created_at->format('d M Y, H:i'),
        ];
    }
}
