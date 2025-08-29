<?php

namespace App\Filament\Shared\Resources\Posts\Schemas;

use App\Enums\Status;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Header Section with Image and Basic Info
                Section::make('Post Overview')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('image_url')
                                    ->label('Featured Image')
                                    ->extraImgAttributes([
                                        'class' => 'rounded-lg object-cover w-full h-auto',
                                        'style' => 'max-height: none; height: auto;'
                                    ])
                                    ->columnSpan(1),

                                Group::make([
                                    TextEntry::make('title')
                                        ->label('Title')
                                        ->weight(FontWeight::Bold)
                                        ->size('lg'),

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
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-eye')
                    ->columnSpanFull(),

                // Post Details Section
                Section::make('Post Information')
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
                    ->columns(2)
                    ->collapsible()
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull(),

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
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull(),

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
                    ->icon('heroicon-o-tag')
                    ->columnSpanFull(),

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
                    ->icon('heroicon-o-exclamation-triangle')
                    ->columnSpanFull(),

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
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull(),
            ]);
    }
}
