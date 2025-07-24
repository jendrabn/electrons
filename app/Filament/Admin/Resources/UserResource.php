<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Role;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->nullable()
                    ->string()
                    ->minLength(5)
                    ->maxLength(30),
                TextInput::make('username')
                    ->nullable()
                    ->string()
                    ->minLength(5)
                    ->maxLength(30)
                    ->alphaDash()
                    ->unique(ignoreRecord: true),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->nullable()
                    ->string()
                    ->tel()
                    ->minLength(10)
                    ->maxLength(15)
                    ->startsWith(['08', '+62', '62']),
                Radio::make('sex')
                    ->nullable()
                    ->string()
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->inline(),
                DatePicker::make('birth_date')
                    ->nullable()
                    ->date()
                    ->maxDate(now()),
                FileUpload::make('avatar')
                    ->nullable()
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->imageCropAspectRatio(1, 1)
                    ->maxSize(1024)
                    ->maxFiles(1)
                    ->directory('avatars'),
                Textarea::make('address')
                    ->nullable()
                    ->string()
                    ->minLength(5)
                    ->maxLength(100),
                TextInput::make('password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('avatar')->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('username')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->colors([
                        'primary' => Role::ADMIN->value,
                        'success' => Role::AUTHOR->value,
                    ])
                    ->getStateUsing(fn(User $record): string => $record->getRoleNames()->first())
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Posts')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sex')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_suspended')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->colors([
                        'danger' => '0',
                        'success' => '1',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filters\SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->options(Role::class),
                Filters\SelectFilter::make('sex')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                Filters\SelectFilter::make('is_suspended')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                    ]),
                Filters\SelectFilter::make('is_verified')
                    ->label('Verified')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ])
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
