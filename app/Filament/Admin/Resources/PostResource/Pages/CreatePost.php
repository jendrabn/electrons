<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Save')
                ->color('primary')
                ->action(function () {
                    $this->form->fill([
                        ...$this->form->getState(),
                        'status' => Status::PENDING->value
                    ]);
                    $this->create();
                }),
            Actions\Action::make('createDraft')
                ->label('Save as Draft')
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
        $data['min_read'] = str_word_count($data['content']) / 200;

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->tags()->sync($this->data['tags']);
    }
}
