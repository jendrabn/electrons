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
                ->label('Tambah Seksi Blog Post')
                ->modalWidth($modalConfig['width'])
                ->modalHeading('Buat Seksi Blog Post')
                ->modalAlignment($modalConfig['alignment'])
                ->schema(PostSectionSchema::getSchema())
                ->mutateDataUsing(fn($data) => PostSectionSchema::mutateDataUsing($data))
                ->successNotificationTitle('Seksi postingan berhasil dibuat'),
        ];
    }
}
