<?php

namespace App\Filament\Admin\Resources\PostSectionResource\Pages;

use App\Filament\Admin\Resources\PostSectionResource;
use App\Models\PostSection;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePostSection extends CreateRecord
{
    protected static string $resource = PostSectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto set order to be the last
        $data['order'] = PostSection::max('order') + 1;
        $data['slug'] = str()->slug($data['name']);

        return $data;
    }
}
