<?php

namespace App\Filament\Admin\Resources\PostSectionResource\Pages;

use App\Filament\Admin\Resources\PostSectionResource;
use App\Models\PostSection;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePostSection extends CreateRecord
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
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Save')
                ->icon('heroicon-s-check'),
            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-o-x-mark'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['order'] = PostSection::max('order') + 1;
        $data['slug'] = str()->slug($data['name']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Post Section Created';
    }
}
