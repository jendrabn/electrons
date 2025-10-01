<?php

namespace App\Filament\Shared\Resources\Posts\Pages;

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
            Action::make('create')
                ->label('Buat')
                ->color('primary')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::PENDING->value,
                    ];

                    $this->create();
                }),
            Action::make('createAnother')
                ->label('Buat & buat lainnya')
                ->color('gray')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::PENDING->value,
                    ];

                    $this->create();
                }),
            Action::make('createDraft')
                ->label('Simpan sebagai Draf')
                ->color('gray')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::DRAFT->value,
                    ];

                    $this->create();
                }),
            Action::make('createPublish')
                ->label('Simpan & Terbitkan')
                ->color('success')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::PUBLISHED->value,
                        'published_at' => now(),
                    ];

                    $this->create();
                })
                ->visible(fn() => auth()->user()->isAdmin()),
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
        return 'Blog Post berhasil dibuat.';
    }
}
