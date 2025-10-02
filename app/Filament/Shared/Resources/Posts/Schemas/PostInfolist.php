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
                Section::make('Ringkasan Blog Post')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('image_url')
                                    ->label('Gambar Utama')
                                    ->extraImgAttributes([
                                        'style' => 'border-radius: 8px; object-fit: cover; width: 100%; height: auto; max-height: 300px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);',
                                    ])
                                    ->columnSpan(1),

                                Group::make([
                                    TextEntry::make('title')
                                        ->label('Judul')
                                        ->weight(FontWeight::Bold)
                                        ->size('lg'),

                                    TextEntry::make('image_caption')
                                        ->label('Keterangan Gambar')
                                        ->placeholder('Tidak ada keterangan')
                                        ->extraAttributes([
                                            'class' => 'italic',
                                        ]),

                                    TextEntry::make('category.name')
                                        ->label('Kategori')
                                        ->badge()
                                        ->color('info'),

                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => Status::tryFrom($state)?->getLabel() ?? ucfirst($state))
                                        ->color(fn ($state) => Status::tryFrom($state)?->GetColor() ?? 'secondary'),
                                ])->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-eye')
                    ->columnSpanFull(),

                // Post Details Section
                Section::make('Informasi Blog Post')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID Blog Post')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable()
                            ->copyMessage('Slug disalin!')
                            ->copyMessageDuration(1500),

                        TextEntry::make('user.name')
                            ->label('Penulis')
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('min_read')
                            ->label('Waktu Baca')
                            ->suffix(' menit')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('views_count')
                            ->label('Total Dilihat')
                            ->numeric()
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-eye'),

                        TextEntry::make('published_at')
                            ->label('Tanggal Terbit')
                            ->dateTime('d M Y, H:i:s')
                            ->placeholder('Belum diterbitkan')
                            ->icon('heroicon-o-calendar'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull(),

                // Content Section
                Section::make('Konten')
                    ->schema([
                        TextEntry::make('teaser')
                            ->label('Ringkasan')
                            ->placeholder('Tidak ada ringkasan')
                            ->prose()
                            ->markdown()
                            ->columnSpanFull(),

                        TextEntry::make('content')
                            ->label('Konten Lengkap')
                            ->html()
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull(),

                // Tags Section
                Section::make('Tag & Kategorisasi')
                    ->schema([
                        TextEntry::make('tags.name')
                            ->label('Tag')
                            ->badge()
                            ->separator(',')
                            ->color('primary')
                            ->placeholder('Belum ada tag'),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-tag')
                    ->columnSpanFull(),

                // Rejection Reason (if applicable)
                Section::make('Detail Moderasi')
                    ->schema([
                        TextEntry::make('rejected_reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Tidak berlaku')
                            ->color('danger')
                            ->prose(),
                    ])
                    ->visible(fn ($record) => $record->status === Status::REJECTED->value)
                    ->collapsible()
                    ->icon('heroicon-o-exclamation-triangle')
                    ->columnSpanFull(),

                // Timestamps Section
                Section::make('Informasi Sistem')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i:s')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success'),

                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i:s')
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('warning'),
                            ]),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull(),
            ]);
    }
}
