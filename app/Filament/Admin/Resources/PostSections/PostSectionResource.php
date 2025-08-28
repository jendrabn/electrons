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
use Filament\Forms\Components\TextInput;
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
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Posts Count')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('order')
                    ->label('Order')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date & Time Created')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Date & Time Updated')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->reorderRecordsTriggerAction(function (Action $action, bool $isReordering) {
                return $action
                    ->button()
                    ->label($isReordering ? 'Finish Reorder' : 'Reorder');
            })
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth($modalConfig['width'])
                    ->modalHeading('Edit Post Section')
                    ->modalAlignment($modalConfig['alignment'])
                    ->schema(PostSectionSchema::getSchema())
                    ->mutateDataUsing(fn($data) => PostSectionSchema::mutateDataUsing($data))
                    ->successNotificationTitle('Post section updated successfully'),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotificationTitle('Post section deleted successfully'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->successNotificationTitle('Post sections deleted successfully'),
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
