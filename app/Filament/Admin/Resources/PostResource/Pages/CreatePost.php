<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    private $additionalData = [];

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Save')
                ->color('primary')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::PENDING->value,
                    ];

                    $this->create();
                }),
            Actions\Action::make('createDraft')
                ->label('Save as Draft')
                ->color('gray')
                ->action(function () {
                    $this->additionalData = [
                        'status' => Status::DRAFT->value
                    ];

                    $this->create();
                }),
            Actions\Action::make('createPublish')
                ->label('Save and Publish')
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
        $data['user_id'] = Auth::id();
        $data['slug'] = str()->slug($data['title']);
        $data['min_read'] = str_word_count($data['content']) / 200;

        $data = array_merge($data, $this->additionalData);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->tags()->sync($this->data['tags']);
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
