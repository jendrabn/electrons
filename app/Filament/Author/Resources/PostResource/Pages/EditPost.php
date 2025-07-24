<?php

namespace App\Filament\Author\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Author\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

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
        return $data;
    }
}
