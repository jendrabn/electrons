<?php

namespace App\Filament\Shared\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('avatar')
                    ->image()
                    ->avatar()
                    ->alignCenter()
                    ->directory('upload/avatars')
                    ->disk('public')
                    ->maxSize(1024),

                $this->getNameFormComponent()
                    ->label('Nama Lengkap')
                    ->required()
                    ->minLength(3)
                    ->maxLength(100),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minLength(3)
                    ->maxLength(100),

                $this->getEmailFormComponent()
                    ->label('Email'),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->nullable()
                    ->string()
                    ->startsWith('62')
                    ->minLength(10)
                    ->maxLength(15),

                Select::make('sex')
                    ->label('Jenis Kelamin')
                    ->nullable()
                    ->string()
                    ->in(['male', 'female'])
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ])
                    ->placeholder('Pilih jenis kelamin'),

                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->nullable()
                    ->minDate(now()->subYears(100))
                    ->maxDate(now()->subYears(10))
                    ->placeholder('Pilih tanggal lahir'),

                Textarea::make('address')
                    ->label('Alamat')
                    ->nullable()
                    ->string()
                    ->minLength(5)
                    ->maxLength(255),

                Textarea::make('bio')
                    ->label('Bio')
                    ->rows(3),

                $this->getPasswordFormComponent()
                    ->label('Password Baru'),

                $this->getPasswordConfirmationFormComponent()
                    ->label('Konfirmasi Password Baru'),

                $this->getCurrentPasswordFormComponent()
                    ->label('Password Saat Ini'),
            ]);
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::ThreeExtraLarge;
    }
}
