<?php

namespace App\Filament\Admin\Resources\Users;

use App\Enums\Role;
use App\Filament\Admin\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'User';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(UserFormSchema::getSchema());
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Basic Information
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name')
                    ->label('Name'),
                TextEntry::make('username')
                    ->label('Username')
                    ->placeholder('Not set'),
                TextEntry::make('email')
                    ->label('Email')
                    ->copyable(),

                // Personal Information
                TextEntry::make('sex')
                    ->label('Gender')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'male' => 'Male',
                        'female' => 'Female',
                        default => 'Not specified',
                    }),
                TextEntry::make('birth_date')
                    ->label('Date of Birth')
                    ->date('F j, Y')
                    ->placeholder('Not set'),
                TextEntry::make('phone')
                    ->label('Phone Number')
                    ->placeholder('Not provided')
                    ->copyable(),
                TextEntry::make('address')
                    ->label('Address')
                    ->placeholder('Not provided'),

                // Role & Statistics
                TextEntry::make('role')
                    ->label('Role')
                    ->badge()
                    ->colors([
                        'primary' => Role::ADMIN->value,
                        'success' => Role::AUTHOR->value,
                    ])
                    ->getStateUsing(fn(User $record): string => $record->getRoleNames()->first()),
                TextEntry::make('posts_count')
                    ->label('Total Posts')
                    ->numeric()
                    ->suffix(' posts')
                    ->color('info'),

                // Account Status
                TextEntry::make('is_suspended')
                    ->label('Account Status')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Suspended' : 'Active')
                    ->color(fn(bool $state): string => $state ? 'danger' : 'success'),
                TextEntry::make('suspend_reason')
                    ->label('Suspension Reason')
                    ->placeholder('No reason')
                    ->visible(fn($record): bool => $record?->is_suspended ?? false),

                // Verification
                TextEntry::make('email_verified_at')
                    ->label('Email Verified')
                    ->formatStateUsing(fn(?string $state): string => $state ? 'Yes' : 'No')
                    ->color(fn(?string $state): string => $state ? 'success' : 'danger'),
                TextEntry::make('google_id')
                    ->label('Google Account')
                    ->formatStateUsing(fn(?string $state): string => $state ? 'Connected' : 'Not Connected')
                    ->color(fn(?string $state): string => $state ? 'success' : 'gray'),

                // Timestamps
                TextEntry::make('created_at')
                    ->label('Registered At')
                    ->dateTime('M j, Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y H:i'),
                TextEntry::make('suspended_at')
                    ->label('Suspended At')
                    ->dateTime('M j, Y H:i')
                    ->visible(fn($record): bool => $record?->suspended_at !== null),
                TextEntry::make('unsuspended_at')
                    ->label('Unsuspended At')
                    ->dateTime('M j, Y H:i')
                    ->visible(fn($record): bool => $record?->unsuspended_at !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        $modalConfig = UserFormSchema::getModalConfig();

        return $table
            ->recordTitleAttribute('User')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('avatar_url')
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
            ->defaultSort('id', 'desc')
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
                ViewAction::make()
                    ->modalWidth($modalConfig['width'])
                    ->modalHeading('User Details')
                    ->modalAlignment($modalConfig['alignment'])
                    ->closeModalByClickingAway(false),
                EditAction::make()
                    ->modalWidth($modalConfig['width'])
                    ->modalHeading('Edit User')
                    ->modalAlignment($modalConfig['alignment'])
                    ->closeModalByClickingAway(false)
                    ->schema(UserFormSchema::getSchema())
                    ->mutateDataUsing(function ($data) use (&$tempRole) {
                        $tempRole = $data['role'] ?? null;
                        unset($data['role']);

                        if (isset($data['password'])) {
                            $data['password'] = bcrypt($data['password']);
                        } else {
                            unset($data['password']);
                        }

                        return $data;
                    })
                    ->after(function (User $record, array $data) use (&$tempRole) {
                        $record->syncRoles([$tempRole]);
                    })
                    ->successNotificationTitle('User updated successfully'),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotificationTitle('User Deleted Successfully'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->successNotificationTitle('Users Deleted Successfully'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
