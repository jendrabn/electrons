<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->minLength(3)
                    ->maxLength(30)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->string()
                    ->minLength(3)
                    ->maxLength(150)
                    ->columnSpanFull()
            ]);
    }
}
