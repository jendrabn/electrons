<?php

namespace App\Filament\Shared\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EditProfile extends BaseEditProfile
{

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('username')->maxLength(50),
                    TextInput::make('email')->required()->email()->maxLength(255),
                    TextInput::make('phone')->maxLength(20),
                    Select::make('sex')->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])->placeholder('Select gender'),
                    DatePicker::make('birth_date'),
                    TextInput::make('address')->maxLength(255),
                ]),
                FileUpload::make('avatar')
                    ->image()
                    ->directory('avatars')
                    ->disk('public')
                    ->maxSize(1024)
                    ->columnSpanFull(),

                TextInput::make('password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(30)
                    ->helperText('Kosongkan jika tidak ingin mengganti password')
                    ->columnSpanFull(),

                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Konfirmasi Password')
                    ->minLength(8)
                    ->maxLength(30)
                    ->columnSpanFull(),

                Textarea::make('bio')->rows(3)->columnSpanFull(),
            ]);
    }

    /**
     * Override to hash password when provided.
     */
    public function update(array $data): void
    {
        // If password provided, ensure confirmation matches and hash it
        if (! empty($data['password'])) {
            $confirm = $data['password_confirmation'] ?? null;
            if ($confirm === null || $confirm !== $data['password']) {
                throw ValidationException::withMessages(['password_confirmation' => ['Konfirmasi password tidak cocok.']]);
            }

            $data['password'] = Hash::make($data['password']);
        }

        // remove confirmation before updating
        if (isset($data['password_confirmation'])) {
            unset($data['password_confirmation']);
        }

        // if password is empty, ensure it's not sent to avoid overwriting
        if (empty($data['password'])) {
            unset($data['password']);
        }

        parent::update($data);
    }
}
