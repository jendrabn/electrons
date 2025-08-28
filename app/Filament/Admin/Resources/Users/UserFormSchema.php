<?php

namespace App\Filament\Admin\Resources\Users;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Spatie\Permission\Models\Role as SpatieRole;

class UserFormSchema
{
    public static function getSchema()
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->string()
                        ->minLength(5)
                        ->maxLength(30),
                    TextInput::make('username')
                        ->required()
                        ->string()
                        ->minLength(5)
                        ->maxLength(30)
                        ->alphaDash()
                        ->unique(ignoreRecord: true),
                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100),
                    Select::make('role')
                        ->label('Role')
                        ->required()
                        ->options(SpatieRole::all()->pluck('name', 'name'))
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->roles->isNotEmpty()) {
                                $component->state($record->getRoleNames()->first());
                            }
                        }),
                    TextInput::make('password')
                        ->required(
                            fn($context) => $context === 'create'
                        )
                        ->password()
                        ->minLength(8)
                        ->maxLength(30)
                        ->helperText(
                            fn($context) => $context === 'edit' ? 'Leave blank if you don\'t want to change the password' : null
                        ),
                ])
        ];
    }

    public static function getModalConfig(): array
    {
        return [
            'width' => Width::FiveExtraLarge,
            'alignment' => Alignment::Start,
        ];
    }
}
