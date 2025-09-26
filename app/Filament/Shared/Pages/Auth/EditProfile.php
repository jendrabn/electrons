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
                    $this->getNameFormComponent(),
                    TextInput::make('username')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->minLength(3)
                        ->maxLength(50),
                    $this->getEmailFormComponent(),
                    TextInput::make('phone')
                        ->nullable()
                        ->string()
                        ->startsWith('62')
                        ->minLength(10)
                        ->maxLength(15),
                    Select::make('sex')
                        ->nullable()
                        ->string()
                        ->in(['male', 'female'])
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                        ])
                        ->placeholder('Select gender'),
                    DatePicker::make('birth_date')
                        ->nullable()
                        ->placeholder('Select birth date'),
                    TextInput::make('address')
                        ->nullable()
                        ->string()
                        ->minLength(5)
                        ->maxLength(255),
                ]),
                FileUpload::make('avatar')
                    ->image()
                    ->directory('upload/avatars')
                    ->disk('public')
                    ->maxSize(1024)
                    ->columnSpanFull(),

                $this->getPasswordFormComponent(),

                Textarea::make('bio')->rows(3)->columnSpanFull(),
            ]);
    }
}
