<?php

namespace App\Filament\Author\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Author\Resources\PostResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected static array $savedTags = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = str()->slug($data['title']);
        $data['min_read'] = str_word_count($data['content']) / 200;
        $data['status'] = Status::PENDING->value;

        self::$savedTags = $data['tags'] ?? [];

        unset($data['tags']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->tags()->sync(self::$savedTags);
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status === Status::PENDING->value) {
            Notification::make()
                ->title('The post cannot be edited until it has been approved.')
                ->body('Please wait for the admin to approve the post.')
                ->danger()
                ->persistent()
                ->send();
        }

        $this->redirect(static::getResource()::getUrl('index'));
    }
}
