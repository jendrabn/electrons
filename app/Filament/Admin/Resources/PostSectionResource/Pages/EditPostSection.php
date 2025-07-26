<?php

namespace App\Filament\Admin\Resources\PostSectionResource\Pages;

use App\Filament\Admin\Resources\PostSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;

class EditPostSection extends EditRecord
{
    protected static string $resource = PostSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('primary')
                ->url($this->getResource()::getUrl('index')),

            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save Changes')
                ->icon('heroicon-s-check'),
            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = str()->slug($data['name']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Post Section Updated';
    }
}
