<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\PostResource;
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

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->action(function () {
                    if ($this->record->status === Status::REJECTED->value) {
                        $this->form->fill([
                            ...$this->form->getState(),
                            'slug' => str()->slug($this->form->getState()['title']),
                            'min_read' => str_word_count($this->form->getState()['content']) / 200,
                            'status' => Status::PENDING->value
                        ]);
                    }
                    $this->create();
                }),
            $this->getCancelFormAction(),
        ];
    }
}
