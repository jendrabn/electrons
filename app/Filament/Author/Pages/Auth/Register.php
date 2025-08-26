<?php

namespace App\Filament\Author\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{

    public function form(Schema $schema): Schema
    {
        return $schema
            ->fields([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    public function handleRegistration(array $data): Model
    {
        $user = $this->getUserModel()::create($data);

        $user->assignRole('author');

        return $user;
    }
}
