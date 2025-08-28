<?php

namespace App\Filament\Author\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('avatar')
                ->label('Avatar')
                ->nullable()
                ->image()
                ->avatar()
                ->imageCropAspectRatio(1, 1)
                ->maxSize(1024)
                ->maxFiles(1)
                ->directory('uploads/avatars')
                ->disk('public')
                ->visibility('public')
                ->alignCenter(),
            $this->getNameFormComponent()
                ->label('Name'),
            TextInput::make('username')
                ->label('Username')
                ->nullable()
                ->string()
                ->minLength(5)
                ->maxLength(30)
                ->alphaDash()
                ->unique(ignoreRecord: true),
            $this->getEmailFormComponent()
                ->label('Email'),
            TextInput::make('phone')
                ->label('Phone')
                ->nullable()
                ->string()
                ->tel()
                ->minLength(10)
                ->maxLength(15)
                ->startsWith(['08', '+62', '62']),
            Radio::make('sex')
                ->label('Gender')
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
                ->label('Address')
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
