<?php

namespace App\Filament\Admin\Resources\PostSections\Pages;

use App\Filament\Admin\Resources\PostSections\PostSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPostSection extends EditRecord
{
    protected static string $resource = PostSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = str()->slug($data['name']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Post section updated successfully.';
    }
}
