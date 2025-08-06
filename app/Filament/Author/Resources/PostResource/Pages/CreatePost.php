<?php

namespace App\Filament\Author\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Author\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected static array $savedTags = [];

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Save')
                ->color('primary')
                ->icon('heroicon-s-check')
                ->action(function () {
                    $this->form->fill([
                        ...$this->form->getState(),
                        'status' => Status::PENDING->value
                    ]);
                    $this->create();
                }),
            Actions\Action::make('createDraft')
                ->label('Save as Draft')
                // ->icon('heroicon-s-pen')
                ->color('gray')
                ->action(function () {
                    $this->form->fill([
                        ...$this->form->getState(),
                        'status' => Status::DRAFT->value
                    ]);
                    $this->create();
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['slug'] = str()->slug($data['title']);
        $data['min_read'] = str_word_count(strip_tags($data['content'])) / 200;
        $data['status'] = Status::PENDING->value;

        self::$savedTags = $data['tags'] ?? [];

        unset($data['tags']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->tags()->sync(self::$savedTags);
    }
}
