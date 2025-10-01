<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserFormSchema;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        $modalConfig = UserFormSchema::getModalConfig();

        return [
            CreateAction::make()
                ->label('Tambah Pengguna')
                ->modalWidth($modalConfig['width'])
                ->modalHeading('Buat Pengguna')
                ->modalAlignment($modalConfig['alignment'])
                ->closeModalByClickingAway(false)
                ->schema(UserFormSchema::getSchema())
                ->mutateDataUsing(function ($data) use (&$tempRole) {
                    $tempRole = $data['role'] ?? null;
                    unset($data['role']);
                    $data['password'] = bcrypt($data['password']);

                    return $data;
                })
                ->after(function (User $record, array $data) use (&$tempRole) {
                    $record->assignRole($tempRole);
                })
                ->successNotificationTitle('Pengguna berhasil dibuat'),
        ];
    }
}
