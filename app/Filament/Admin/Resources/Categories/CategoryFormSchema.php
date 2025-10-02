<?php

namespace App\Filament\Admin\Resources\Categories;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;

class CategoryFormSchema
{
    public static function getSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama')
                ->required()
                ->string()
                ->minLength(3)
                ->maxLength(30)
                ->unique(ignoreRecord: true)
                ->columnSpanFull(),
            Textarea::make('description')
                ->label('Deskripsi')
                ->required()
                ->string()
                ->minLength(3)
                ->maxLength(150)
                ->columnSpanFull(),
            ColorPicker::make('color')
                ->label('Warna')
                ->required()
                ->default('#3B82F6')
                ->hex()
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
