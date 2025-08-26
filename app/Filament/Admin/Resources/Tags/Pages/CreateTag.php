<?php

namespace App\Filament\Admin\Resources\Tags\Pages;

use App\Filament\Admin\Resources\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = str($data['name'])->slug();
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tag created successfully.';
    }
}
