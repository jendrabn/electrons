<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                    ->nullable()
                    ->image()
                    ->imageEditor()
                    ->avatar()
                    ->imageCropAspectRatio(1, 1)
                    ->maxSize(1024)
                    ->maxFiles(1)
                    ->directory('avatars')
                    ->alignCenter(),
                $this->getNameFormComponent(),
                TextInput::make('username')
                    ->nullable()
                    ->string()
                    ->minLength(5)
                    ->maxLength(30)
                    ->alphaDash()
                    ->unique(ignoreRecord: true),
                $this->getEmailFormComponent()
                    ->label('Email'),
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
                    ->label('Birth Date')
                    ->nullable()
                    ->date()
                    ->maxDate(now()),
                Textarea::make('address')
                    ->nullable()
                    ->string()
                    ->minLength(5)
                    ->maxLength(100),
                $this->getPasswordFormComponent()
                    ->label('New Password'),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    public function getMaxContentWidth(): string
    {
        return '3xl';
    }
}
