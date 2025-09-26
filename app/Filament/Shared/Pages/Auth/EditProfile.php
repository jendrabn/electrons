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

                $this->getPasswordFormComponent(),

                Textarea::make('bio')->rows(3)->columnSpanFull(),
            ]);
    }
}
