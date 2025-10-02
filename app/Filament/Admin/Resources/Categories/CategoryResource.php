<?php

namespace App\Filament\Admin\Resources\Categories;

use App\Filament\Admin\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Category';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(CategoryFormSchema::getSchema());
    }

    public static function table(Table $table): Table
    {
        $modalConfig = CategoryFormSchema::getModalConfig();

        return $table
            ->recordTitleAttribute('Category')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')

                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('NAMA')

                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('SLUG')

                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('DESKRIPSI')

                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label('WARNA')
                    ->badge()
                    ->color(fn ($record) => $record->color)
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->sortable(),
                TextColumn::make('posts_count')
                    ->label('JUMLAH BLOG POST')

                    ->counts('posts')
                    ->sortable(),
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
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth($modalConfig['width'])
                    ->modalHeading('Ubah Kategori')
                    ->modalAlignment($modalConfig['alignment'])
                    ->schema(CategoryFormSchema::getSchema())
                    ->mutateDataUsing(fn ($data) => CategoryFormSchema::mutateDataUsing($data))
                    ->successNotificationTitle('Kategori berhasil diperbarui'),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotificationTitle('Kategori berhasil dihapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->successNotificationTitle('Kategori berhasil dihapus'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
