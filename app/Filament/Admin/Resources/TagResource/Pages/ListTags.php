<?php

namespace App\Filament\Admin\Resources\TagResource\Pages;

use App\Filament\Admin\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Tag')
                ->icon('heroicon-s-plus')
                ->modal()
                ->modalHeading('Add Tag')
                ->modalSubmitActionLabel('Save')
                ->mutateFormDataUsing((fn($data) => array_merge($data, ['slug' => str()->slug($data['name'])])))
        ];
    }
}
