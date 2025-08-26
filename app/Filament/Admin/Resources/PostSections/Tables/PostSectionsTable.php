<?php

namespace App\Filament\Admin\Resources\PostSections\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->reorderRecordsTriggerAction(function (Action $action, bool $isReordering) {
                return $action
                    ->button()
                    ->label($isReordering ? 'Finish Reorder' : 'Reorder');
            });
    }
}
