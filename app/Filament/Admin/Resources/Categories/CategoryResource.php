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
use Illuminate\Support\HtmlString;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $recordTitleAttribute = 'name';

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
                    ->html()
                    ->formatStateUsing(function (?string $state): HtmlString {
                        if (blank($state)) {
                            return new HtmlString('—');
                        }

                        $hex = strtoupper($state);
                        $safeHex = e($hex);

                        $markup = sprintf(
                            '<span style="display:inline-flex;align-items:center;gap:0.5rem;font-weight:600;letter-spacing:0.05em;text-transform:uppercase;color:#1f2937">'
                            .'<span style="width:1.25rem;height:1.25rem;border-radius:999px;border:1px solid rgba(15,23,42,0.15);box-shadow:0 1px 4px rgba(15,23,42,0.18);background-color:%1$s"></span>'
                            .'<span>%2$s</span>'
                            .'</span>',
                            $safeHex,
                            $safeHex
                        );

                        return new HtmlString($markup);
                    })
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
