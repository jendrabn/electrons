<?php

namespace App\Filament\Admin\Resources\Tags;

use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;

class TagFormSchema
{
    public static function getSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama Tag')
                ->required()
                ->string()
                ->minLength(3)
                ->maxLength(30)
                ->unique(ignoreRecord: true)
                ->columnSpanFull(),
        ];
    }

    public static function mutateDataUsing(array $data): array
    {
        $data['slug'] = str()->slug($data['name']);

        return $data;
    }

    public static function getModalConfig(): array
    {
        return [
            'width' => Width::ExtraLarge,
            'alignment' => Alignment::Start,
        ];
    }
}
