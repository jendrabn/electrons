<?php

namespace App\Filament\Admin\Resources\PostSections;

use App\Filament\Admin\Resources\PostSections\Pages\ManagePostSections;
use App\Models\PostSection;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostSectionResource extends Resource
{
    protected static ?string $model = PostSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bars2;

    protected static ?string $recordTitleAttribute = 'Post Section';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(PostSectionSchema::getSchema());
    }

    public static function table(Table $table): Table
    {
        $modalConfig = PostSectionSchema::getModalConfig();

        return $table
            ->recordTitleAttribute('Post Section')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->size('sm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->size('sm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('SLUG')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('JUMLAH BLOG POST')
                    ->size('sm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('order')
                    ->label('URUTAN')
                    ->size('sm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('TANGGAL & WAKTU DIBUAT')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('TANGGAL & WAKTU DIPERBARUI')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->reorderRecordsTriggerAction(function (Action $action, bool $isReordering) {
                return $action
                    ->button()
                    ->label($isReordering ? 'Selesai Mengurutkan' : 'Atur Urutan');
            })
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth($modalConfig['width'])
                    ->modalHeading('Ubah Seksi Blog Post')
                    ->modalAlignment($modalConfig['alignment'])
                    ->schema(PostSectionSchema::getSchema())
                    ->mutateDataUsing(fn($data) => PostSectionSchema::mutateDataUsing($data))
                    ->successNotificationTitle('Seksi postingan berhasil diperbarui'),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotificationTitle('Seksi postingan berhasil dihapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->successNotificationTitle('Seksi postingan berhasil dihapus'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePostSections::route('/'),
        ];
    }
}
