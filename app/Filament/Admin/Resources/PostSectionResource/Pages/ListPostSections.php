<?php

namespace App\Filament\Admin\Resources\PostSectionResource\Pages;

use App\Filament\Admin\Resources\PostSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPostSections extends ListRecords
{
    protected static string $resource = PostSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
