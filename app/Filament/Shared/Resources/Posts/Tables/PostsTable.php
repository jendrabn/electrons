<?php

namespace App\Filament\Shared\Resources\Posts\Tables;

use App\Enums\Status;
use App\Filament\Admin\Resources\AuditLogs\AuditLogResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('image_url')
                    ->label('GAMBAR')
                    ->imageSize(40)
                    ->extraImgAttributes([
                        'style' => 'object-fit: cover; border: 1px solid #fbbf24; padding: 1px; border-radius: 4px;',
                    ]),

                TextColumn::make('image_caption')
                    ->label('KETERANGAN GAMBAR')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('JUDUL')
                    ->lineClamp(2)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('SLUG')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('PENULIS')
                    ->words(2, '')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => auth()->user()->isAdmin()),

                TextColumn::make('category.name')
                    ->label('KATEGORI')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tags.name')
                    ->label('TAG')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('STATUS')
                    ->badge()
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Status::tryFrom($state)?->getLabel() ?? ucfirst($state))
                    ->color(fn ($state) => Status::tryFrom($state)?->GetColor() ?? 'secondary')
                    ->tooltip(fn (Post $record) => $record->status === Status::REJECTED->value ? 'Reason: '.$record->rejected_reason : null),

                TextColumn::make('min_read')
                    ->label('MENIT BACA')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('views_count')
                    ->label('JUMLAH DILIHAT')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('TANGGAL & WAKTU DIBUAT')
                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('TANGGAL & WAKTU DIPERBARUI')
                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Status::class),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('tags')
                    ->label('Tag')
                    ->relationship('tags', 'name'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('ChangeStatus')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-cog')
                        ->schema([
                            Select::make('status')
                                ->label('Status')
                                ->required()
                                ->options(Status::class)
                                ->enum(Status::class)
                                ->live(),
                            Textarea::make('rejected_reason')
                                ->label('Alasan Penolakan')
                                ->visible(fn (Get $get) => $get('status') === Status::REJECTED)
                                ->dehydrated(fn (Get $get) => $get('status') === Status::REJECTED)
                                ->required(fn (Get $get) => $get('status') === Status::REJECTED),
                        ])
                        ->action(function (Post $record, array $data) {
                            $data['status'] = $data['status']->value;

                            if ($data['status'] !== Status::REJECTED) {
                                $data['rejected_reason'] = null;
                            }

                            if ($data['status'] === Status::PUBLISHED) {
                                $data['published_at'] = now();
                            }

                            $record->update($data);
                        })
                        ->successNotificationTitle('Status berhasil diperbarui')
                        ->visible(fn () => auth()->user()->isAdmin()),

                    Action::make('View Data Changes')
                        ->label('Lihat Perubahan Data')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->action(function (Post $record) {
                            $latestAudit = $record->audits()
                                ->orderBy('created_at', 'desc')
                                ->first();

                            if ($latestAudit) {
                                return redirect()
                                    ->to(AuditLogResource::getUrl('view', ['record' => $latestAudit]))
                                    ->with('success', 'Menampilkan log audit terbaru untuk postingan ini.');
                            } else {
                                Notification::make()
                                    ->title('Log audit tidak ditemukan')
                                    ->body('Blog Post ini belum memiliki log audit.')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->visible(fn (Post $record) => $record->audits()->exists() && auth()->user()->isAdmin()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
