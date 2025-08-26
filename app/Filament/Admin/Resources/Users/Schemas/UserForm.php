<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar')
                    ->nullable()
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->imageCropAspectRatio(1, 1)
                    ->maxSize(1024)
                    ->maxFiles(1)
                    ->directory('avatars')
                    ->columnSpanFull()
                    ->alignCenter(),
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
                    ->maxLength(255),
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
}
