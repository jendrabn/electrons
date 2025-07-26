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
            Actions\CreateAction::make()
                ->label('Add Post Section')
                ->icon('heroicon-s-plus')
                ->modal()
                ->modalHeading('Add Post Section')
                ->modalSubmitActionLabel('Save')
                ->modalCancelActionLabel('Cancel')
                ->mutateFormDataUsing((fn($data) => array_merge($data, ['slug' => str()->slug($data['name'])])))
        ];
    }
}
