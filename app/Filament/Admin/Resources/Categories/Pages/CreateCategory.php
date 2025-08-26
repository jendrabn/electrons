<?php

namespace App\Filament\Admin\Resources\Categories\Pages;

use App\Filament\Admin\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = str($data['name'])->slug();
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Category created successfully.';
    }
}
