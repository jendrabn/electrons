<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use App\Enums\Status; // sesuaikan namespace enum Anda
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header Section with Image and Basic Info
                Section::make('Post Overview')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    ImageEntry::make('image_url')
                                        ->label('Featured Image')
                                        ->width('100%')
                                        ->extraImgAttributes([
                                            'class' => 'rounded-lg object-cover w-full h-auto',
                                            'style' => 'max-height: none; height: auto;'
                                        ])
                                        ->columnSpan(1),

                                    Group::make([
                                        TextEntry::make('title')
                                            ->label('Title')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntry\TextEntrySize::Large),

                                        TextEntry::make('image_caption')
                                            ->label('Image Caption')
                                            ->placeholder('No caption provided')
                                            ->extraAttributes([
                                                'class' => 'italic',
                                            ]),

                                        TextEntry::make('category.name')
                                            ->label('Category')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->formatStateUsing(fn($state) => Status::tryFrom($state)?->getLabel() ?? ucfirst($state))
                                            ->color(fn($state) => Status::tryFrom($state)?->GetColor() ?? 'secondary'),
                                    ])->columnSpan(1),
                                ])
                        ])->from('md')
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-eye'),

                // Post Details Section
                Section::make('Post Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Post ID')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('slug')
                                    ->label('Slug')
                                    ->copyable()
                                    ->copyMessage('Slug copied!')
                                    ->copyMessageDuration(1500),

                                TextEntry::make('user.name')
                                    ->label('Author')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('min_read')
                                    ->label('Reading Time')
                                    ->suffix(' minutes')
                                    ->badge()
                                    ->color('warning'),

                                TextEntry::make('views_count')
                                    ->label('Total Views')
                                    ->numeric()
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-eye'),

                                TextEntry::make('published_at')
                                    ->label('Published Date')
                                    ->dateTime('d M Y, H:i:s')
                                    ->placeholder('Not published yet')
                                    ->icon('heroicon-o-calendar'),
                            ])
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->icon('heroicon-o-information-circle'),

                // Content Section
                Section::make('Content')
                    ->schema([
                        TextEntry::make('teaser')
                            ->label('Teaser')
                            ->placeholder('No teaser provided')
                            ->prose()
                            ->markdown()
                            ->columnSpanFull(),

                        TextEntry::make('content')
                            ->label('Full Content')
                            ->html()
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-document-text'),

                // Tags Section
                Section::make('Tags & Categorization')
                    ->schema([
                        TextEntry::make('tags.name')
                            ->label('Tags')
                            ->badge()
                            ->separator(',')
                            ->color('primary')
                            ->placeholder('No tags assigned'),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-tag'),

                // Rejection Reason (if applicable)
                Section::make('Moderation Details')
                    ->schema([
                        TextEntry::make('rejected_reason')
                            ->label('Rejection Reason')
                            ->placeholder('Not applicable')
                            ->color('danger')
                            ->prose(),
                    ])
                    ->visible(fn($record) => $record->status === Status::REJECTED->value)
                    ->collapsible()
                    ->icon('heroicon-o-exclamation-triangle'),

                // Timestamps Section
                Section::make('System Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('d M Y, H:i:s')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success'),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('d M Y, H:i:s')
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('warning'),
                            ])
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-clock'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make('backToList')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square'),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),

            Actions\Action::make('changeStatus')
                ->label('Change Status')
                ->icon('heroicon-o-cog')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options(Status::class)
                        ->live(),
                    \Filament\Forms\Components\Textarea::make('rejected_reason')
                        ->label('Rejection Reason')
                        ->visible(fn($get) => $get('status') === Status::REJECTED->value)
                ])
                ->action(function (array $data) {
                    if ($data['status'] !== Status::REJECTED->value) {
                        $data['rejected_reason'] = null;
                    }

                    if ($data['status'] === Status::PUBLISHED->value) {
                        $data['published_at'] = now();
                    }

                    $this->record->update($data);

                    $this->refreshFormData([
                        'status',
                        'rejected_reason',
                        'published_at'
                    ]);
                }),

            Actions\Action::make('viewOnSite')
                ->label('View on Site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('success')
                ->url(fn() => route('posts.show', $this->record->slug))
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->status === Status::PUBLISHED->value),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add custom widgets here if needed
        ];
    }

    public function getTitle(): string
    {
        return 'View Post';
    }

    protected function getHeaderTabsColor(): string
    {
        return 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return 'View Post';
    }
}
