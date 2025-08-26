<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Enums\Role;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('username')
                    ->label('Username')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->colors([
                        'primary' => Role::ADMIN->value,
                        'success' => Role::AUTHOR->value,
                    ])
                    ->getStateUsing(fn(User $record): string => $record->getRoleNames()->first())
                    ->searchable(),
                TextColumn::make('posts_count')
                    ->label('Posts Count')
                    ->counts('posts')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sex')
                    ->label('Gender')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Address')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_suspended')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->colors([
                        'danger' => '0',
                        'success' => '1',
                    ])
                    ->getStateUsing(fn(User $record): string => $record->is_suspended ? 'Suspended' : 'Active')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->label('Date & Time Verified')
                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date & Time Created')
                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i:s')
                    ->label('Date & Time Updated')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable()
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->options(Role::class),
                SelectFilter::make('sex')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                SelectFilter::make('is_suspended')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('is_verified')
                    ->label('Verified')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ])
            ])
            ->recordActions([
                ViewAction::make(),
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
            ->defaultSort('id', 'desc');
    }
}
