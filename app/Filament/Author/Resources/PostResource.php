<?php

namespace App\Filament\Author\Resources;

use App\Enums\Status;
use App\Filament\Author\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Doctrine\DBAL\Schema\Column;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\Role;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Placeholder::make('rejected_reason')
                    ->label('Rejected Reason')
                    ->content(fn($record) => $record->rejected_reason)
                    ->visible(fn($record) => $record?->status === Status::REJECTED->value),

                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->required()
                    ->options(Category::all()->pluck('name', 'id'))
                    ->searchable()
                    ->hintIcon('heroicon-o-information-circle', 'Select the category that matches the content topic')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->minLength(3)
                    ->maxLength(100)
                    ->hintIcon('heroicon-o-information-circle', 'Provide a clear title that represents the content topic (recommended not more than eight words)')
                    ->columnSpanFull(),

                Forms\Components\Fieldset::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->required()
                            ->image()
                            ->directory('categories')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload images in JPEG, PNG, or WebP format. Maximum 2MB.')
                            ->hintIcon('heroicon-o-information-circle', 'Upload images in JPEG, PNG, or WebP format. Maximum 2MB.')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('image_caption')
                            ->label('Caption')
                            ->hintIcon('heroicon-o-information-circle', 'Add an image caption to help readers understand the image.')
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull(),

                TiptapEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->hintIcon('heroicon-o-information-circle', 'Write content clearly and well')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('teaser')
                    ->label('Teaser')
                    ->nullable()
                    ->minLength(3)
                    ->maxLength(150)
                    ->hintIcon('heroicon-o-information-circle', 'Provide a summary or essence of the content topic to help readers find it easily in search engines.')
                    ->columnSpanFull(),

                Forms\Components\Select::make('tags')
                    ->label('Tags')
                    ->required()
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->searchable()
                    ->preload()
                    ->hintIcon('heroicon-o-information-circle', 'Add Tags (keywords) related to the content. Use the Recommendation button to get automatically recommended keyword Tags')
                    ->columnSpanFull()
                    ->validationAttribute('tags')
                    ->dehydrated()
                    ->live()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),

                Tables\Columns\TextColumn::make('image_caption')
                    ->label('Image Caption')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->lineClamp(2)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->lineClamp(2)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => Status::tryFrom($state)?->getLabel() ?? ucfirst($state))
                    ->color(fn($state) => Status::tryFrom($state)?->GetColor() ?? 'secondary')
                    ->tooltip(fn(Post $record) => $record->status === Status::REJECTED->value ? 'Reason: ' . $record->rejected_reason : null)
                    ->searchable(),

                Tables\Columns\TextColumn::make('min_read')
                    ->label('Min Read')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views Count')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time Created')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Date & Time Updated')
                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Status::class),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('tags')
                    ->relationship('tags', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->user()->id);
    }

    public static function maxContentWidth(): string
    {
        return 'full';
    }
}
