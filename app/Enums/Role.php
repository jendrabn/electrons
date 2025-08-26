<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel, HasColor
{
    case ADMIN = 'admin';
    case AUTHOR = 'author';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::AUTHOR => 'Author',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ADMIN => 'primary',
            self::AUTHOR => 'success',
        };
    }
}
