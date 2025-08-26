<?php

namespace App\Filament\Admin\Resources\Posts\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    private $additionalData = [];

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::PENDING->value,
                    ];

                    $this->create();
                }),
            $this->getCreateAnotherFormAction()
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::PENDING->value,
                    ];

                    $this->create();
                }),
            Action::make('createDraft')
                ->label('Save as Draft')
                ->color('gray')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::DRAFT->value
                    ];

                    $this->create();
                }),
            Action::make('createPublish')
                ->label('Save & Publish')
                ->color('success')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::PUBLISHED->value,
                        'published_at' => now()
                    ];

                    $this->create();
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        $data['slug'] = str()->slug($data['title']);
        $data['min_read'] = str_word_count($data['content']) / 200;

        $data = array_merge($data, $this->additionalData);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->tags()->sync($this->data['tags']);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Post created successfully.';
    }
}
