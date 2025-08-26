<?php

namespace App\Filament\Admin\Resources\PostSections\Pages;

use App\Filament\Admin\Resources\PostSections\PostSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPostSections extends ListRecords
{
    protected static string $resource = PostSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
