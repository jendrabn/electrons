<?php

namespace App\Filament\Admin\Resources\Categories\Pages;

use App\Filament\Admin\Resources\Categories\CategoryFormSchema;
use App\Filament\Admin\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        $modalConfig = CategoryFormSchema::getModalConfig();

        return [
            CreateAction::make()
                ->label('Tambah Kategori')
                ->modalWidth($modalConfig['width'])
                ->modalHeading('Buat Kategori')
                ->modalAlignment($modalConfig['alignment'])
                ->schema(CategoryFormSchema::getSchema())
                ->mutateDataUsing(fn ($data) => CategoryFormSchema::mutateDataUsing($data))
                ->successNotificationTitle('Kategori berhasil dibuat'),
        ];
    }
}
