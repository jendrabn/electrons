<?php

namespace App\Filament\Admin\Resources\Tags\Pages;

use App\Filament\Admin\Resources\Tags\TagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = str($data['name'])->slug();
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Tag updated successfully.';
    }
}
