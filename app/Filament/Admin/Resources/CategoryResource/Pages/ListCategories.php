<?php

namespace App\Filament\Admin\Resources\CategoryResource\Pages;

use App\Filament\Admin\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Category')
                ->icon('heroicon-s-plus')
                ->modal()
                ->modalHeading('Add Category')
                ->modalSubmitActionLabel('Save')
                ->modalCancelActionLabel('Cancel')
                ->mutateFormDataUsing(function (array $data) {
                    return array_merge($data, [
                        'slug' => str($data['name'])->slug(),
                    ]);
                }),
        ];
    }
}
