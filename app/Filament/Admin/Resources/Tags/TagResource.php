<?php

namespace App\Filament\Admin\Resources\Tags;

use App\Filament\Admin\Resources\Tags\Pages\ManageTags;
use App\Models\Tag;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static ?string $modelLabel = 'Tag';

    protected static ?string $pluralModelLabel = 'Tag';

    protected static ?string $navigationLabel = 'Tag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tag')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        $modalConfig = TagFormSchema::getModalConfig();

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->size('sm')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->size('sm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('SLUG')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('posts_count')
                    ->label('JUMLAH BLOG POST')
                    ->size('sm')
                    ->counts('posts')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('TANGGAL & WAKTU DIBUAT')
                    ->size('sm')
                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('TANGGAL & WAKTU DIPERBARUI')
                    ->size('sm')
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
                    ->modalHeading('Ubah Tag')
                    ->modalAlignment($modalConfig['alignment'])
                    ->schema(TagFormSchema::getSchema())
                    ->mutateDataUsing(fn($data) => TagFormSchema::mutateDataUsing($data))
                    ->successNotificationTitle('Tag berhasil diperbarui'),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotificationTitle('Tag berhasil dihapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->successNotificationTitle('Tag berhasil dihapus'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTags::route('/'),
        ];
    }
}
