<?php

namespace App\Filament\Admin\Resources\Tags\Pages;

use App\Filament\Admin\Resources\Tags\TagFormSchema;
use App\Filament\Admin\Resources\Tags\TagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        $modalConfig = TagFormSchema::getModalConfig();

        return [
            CreateAction::make()
                ->label('Tambah Tag')
                ->modalWidth($modalConfig['width'])
                ->modalHeading('Buat Tag')
                ->modalAlignment($modalConfig['alignment'])
                ->schema(TagFormSchema::getSchema())
                ->mutateDataUsing(fn ($data) => TagFormSchema::mutateDataUsing($data))
                ->successNotificationTitle('Tag berhasil dibuat'),
        ];
    }
}
