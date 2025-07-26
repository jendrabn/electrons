<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\PostResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    // public function getTitle(): string
    // {
    //     return 'View Post';
    // }

    // public function getBreadcrumb(): string
    // {
    //     return 'View';
    // }

    // protected function getHeaderActions(): array
    // {
    //     return [];
    // }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->make()
            ->schema([
                Section::make('Post Overview')
                    ->schema([
                        TextEntry::make('title')->label('Title'),
                        TextEntry::make('slug')->label('Slug'),
                        TextEntry::make('category.name')->label('Category'),
                        TextEntry::make('tags.name')
                            ->label('Tags')
                            ->badge(),
                        TextEntry::make('user.name')->label('Author'),
                        // TextEntry::make('status')
                        //     ->label('Status')
                        //     ->badge()
                        //     ->formatStateUsing(fn($state) => Status::tryFrom($state)?->getLabel() ?? ucfirst($state))
                        //     ->color(fn($state) => Status::tryFrom($state)?->getColor() ?? 'gray'),
                        // TextEntry::make('rejected_reason')
                        //     ->label('Rejected Reason')
                        //     ->visible(fn($record) => $record->status === Status::REJECTED->value),
                    ])
                    ->columns(2),

                Section::make('Image')
                    ->schema([
                        ImageEntry::make('image')->label('Featured Image'),
                        TextEntry::make('image_caption')->label('Image Caption'),
                    ])
                    ->columns(2),

                Section::make('Content')
                    ->schema([
                        TextEntry::make('teaser')->label('Teaser'),
                        TextEntry::make('content')->label('Content')->markdown(),
                    ])
                    ->columns(1),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('min_read')->label('Estimated Read Time')->suffix('min'),
                        TextEntry::make('views_count')->label('Views'),
                        TextEntry::make('created_at')->label('Created At')->dateTime('d M Y H:i:s'),
                        TextEntry::make('updated_at')->label('Updated At')->dateTime('d M Y H:i:s'),
                    ])
                    ->columns(2),

                // Section::make('Data Changes')
                //     ->description('Before and after values of the changed data')
                //     ->icon('heroicon-o-arrow-path')
                //     ->visible(fn($record) => !empty($record->old_values) || !empty($record->new_values))
                //     ->schema([
                //         KeyValueEntry::make('old_values')
                //             ->label('Previous Values')
                //             ->visible(fn($record) => !empty($record->old_values))
                //             ->columnSpanFull(fn($record) => empty($record->new_values)),

                //         KeyValueEntry::make('new_values')
                //             ->label('New Values')
                //             ->visible(fn($record) => !empty($record->new_values))
                //             ->columnSpanFull(fn($record) => empty($record->old_values)),
                //     ])
                //     ->columns(1),
            ]);
    }
}
