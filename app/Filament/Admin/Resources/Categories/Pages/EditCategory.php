<?php

namespace App\Filament\Admin\Resources\Categories\Pages;

use App\Filament\Admin\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = str($data['name'])->slug();
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Category updated successfully.';
    }
}
