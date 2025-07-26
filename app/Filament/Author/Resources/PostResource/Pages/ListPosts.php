<?php

namespace App\Filament\Author\Resources\PostResource\Pages;

use App\Filament\Author\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Post')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path'),
        ];
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }
}
