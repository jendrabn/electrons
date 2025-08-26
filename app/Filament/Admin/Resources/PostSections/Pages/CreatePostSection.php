<?php

namespace App\Filament\Admin\Resources\PostSections\Pages;

use App\Filament\Admin\Resources\PostSections\PostSectionResource;
use App\Models\PostSection;
use Filament\Resources\Pages\CreateRecord;

class CreatePostSection extends CreateRecord
{
    protected static string $resource = PostSectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['order'] = PostSection::max('order') + 1;
        $data['slug'] = str()->slug($data['name']);

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Post section created successfully.';
    }
}
