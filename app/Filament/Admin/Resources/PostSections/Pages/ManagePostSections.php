<?php

namespace App\Filament\Admin\Resources\PostSections\Pages;

use App\Filament\Admin\Resources\PostSections\PostSectionResource;
use App\Filament\Admin\Resources\PostSections\PostSectionSchema;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePostSections extends ManageRecords
{
    protected static string $resource = PostSectionResource::class;

    protected function getHeaderActions(): array
    {
        $modalConfig = PostSectionSchema::getModalConfig();

        return [
            CreateAction::make()
                ->label('Add Post Section')
                ->modalWidth($modalConfig['width'])
                ->modalHeading('Create Post Section')
                ->modalAlignment($modalConfig['alignment'])
                ->schema(PostSectionSchema::getSchema())
                ->mutateDataUsing(fn($data) => PostSectionSchema::mutateDataUsing($data))
                ->successNotificationTitle('Post section created successfully'),
        ];
    }
}
