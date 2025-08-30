<?php

namespace App\Filament\Shared\Resources\Posts\Schemas;

use App\Enums\Status;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\TextSize;
use Malzariey\FilamentLexicalEditor\LexicalEditor;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('rejected_reason')
                    ->label('Rejected Reason')
                    ->state(fn($record) => $record->rejected_reason ?? '-')
                    ->visible(fn($record) => $record?->status === Status::REJECTED->value)
                    ->color('danger')
                    ->inlineLabel()
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->label('Category')
                    ->required()
                    ->options(Category::all()
                        ->pluck('name', 'id'))
                    ->hintIcon('heroicon-o-information-circle', 'Select the category that matches the content topic')
                    ->inlineLabel()
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->string()
                    ->minLength(3)
                    ->maxLength(100)
                    ->hintIcon('heroicon-o-information-circle', 'Provide a clear title that represents the content topic (recommended not more than eight words)')
                    ->inlineLabel()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->directory('uploads')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios(['16:9', '4:3', '1:1',])
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Upload images in JPEG, PNG, or WebP format. Maximum 2MB.')
                    ->hintIcon('heroicon-o-information-circle', 'Upload images in JPEG, PNG, or WebP format. Maximum 2MB.')
                    ->inlineLabel()
                    ->columnSpanFull(),
                TextInput::make('image_caption')
                    ->label('Image Caption')
                    ->hintIcon('heroicon-o-information-circle', 'Add an image caption to help readers understand the image.')
                    ->inlineLabel()
                    ->columnSpanFull(),
                LexicalEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->hintIcon('heroicon-o-information-circle', 'Write content clearly and well')
                    ->columnSpanFull(),
                TextInput::make('teaser')
                    ->label('Teaser')
                    ->nullable()
                    ->minLength(3)
                    ->maxLength(150)
                    ->hintIcon('heroicon-o-information-circle', 'Provide a summary or essence of the content topic to help readers find it easily in search engines.')
                    ->inlineLabel()
                    ->columnSpanFull(),
                Select::make('tags')
                    ->label('Tags')
                    ->relationship(name: 'tags', titleAttribute: 'name', modifyQueryUsing: fn($query) => $query
                        ->orderBy('name', 'asc'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->hintIcon('heroicon-o-information-circle', 'Add Tags (keywords) related to the content. Use the Recommendation button to get automatically recommended keyword Tags')
                    ->inlineLabel()
                    ->columnSpanFull(),
            ]);
    }
}
