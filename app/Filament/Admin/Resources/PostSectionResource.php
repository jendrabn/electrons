<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostSectionResource\Pages;
use App\Filament\Admin\Resources\PostSectionResource\RelationManagers;
use App\Models\PostSection;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostSectionResource extends Resource
{
    protected static ?string $model = PostSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Section Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)
                    ->columnSpanFull(),

                Forms\Components\Select::make('posts')
                    ->label('Posts')
                    ->required()
                    ->multiple()
                    ->relationship(
                        name: 'posts',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn(Builder $query) => $query
                            ->where('posts.status', 'published')
                            ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
                            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                            ->with(['category', 'user'])
                            ->select('posts.*')
                            ->orderBy('posts.created_at', 'desc')
                    )
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $category = $record->category?->name ?? 'No Category';
                        $author = $record->user?->name ?? 'No Author';
                        $date = $record->created_at->format('d M Y');

                        return new \Illuminate\Support\HtmlString("
                            <div class='py-2'>
                                <div class='flex items-center justify-between mb-1'>
                                    <span class='font-semibold text-gray-900 dark:text-white'>
                                        #{$record->id} - {$record->title}
                                    </span>
                                </div>
                                <div class='text-xs text-gray-500 dark:text-gray-400 space-y-1'>
                                    <div>👤 Author: <span class='text-green-600 dark:text-green-400'>{$author}</span></div>
                                    <div>📂 Category: <span class='text-purple-600 dark:text-purple-400'>{$category}</span></div>
                                    <div>📅 Created: <span class='text-blue-600 dark:text-blue-400'>{$date}</span></div>
                                </div>
                            </div>
                        ");
                    })
                    ->allowHtml()
                    ->searchable(['posts.title', 'categories.name', 'users.name'])
                    ->preload()
                    ->placeholder('Select posts to add to section')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Posts Count')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Order')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time Created')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Date & Time Updated')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->reorderRecordsTriggerAction(function (Tables\Actions\Action $action, bool $isReordering) {
                return $action
                    ->button()
                    ->label($isReordering ? 'Finish Reorder' : 'Reorder');
            })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostSections::route('/'),
            'create' => Pages\CreatePostSection::route('/create'),
            'edit' => Pages\EditPostSection::route('/{record}/edit'),
        ];
    }
}
