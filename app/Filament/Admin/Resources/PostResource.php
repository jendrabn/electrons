<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Status;
use App\Filament\Admin\Resources\PostResource\Pages;
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
use Filament\Notifications\Notification;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

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
                            ->label('Image Caption')
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
                    ->multiple()
                    ->relationship('tags')
                    ->options(Tag::all()->pluck('name', 'id'))
                    ->hintIcon('heroicon-o-information-circle', 'Add Tags (keywords) related to the content. Use the Recommendation button to get automatically recommended keyword Tags')
                    ->columnSpanFull(),
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
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->lineClamp(2)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->words(2, '')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Tags')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->toggleable()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => Status::tryFrom($state)?->getLabel() ?? ucfirst($state))
                    ->color(fn($state) => Status::tryFrom($state)?->GetColor() ?? 'secondary')
                    ->tooltip(fn(Post $record) => $record->status === Status::REJECTED->value ? 'Reason: ' . $record->rejected_reason : null),

                Tables\Columns\TextColumn::make('min_read')
                    ->label('Min Read')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views Count')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

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
                    Tables\Actions\Action::make('Change Status')
                        ->label('Change Status')
                        ->icon('heroicon-o-cog')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->required()
                                ->options(Status::class)
                                ->live(),
                            Forms\Components\Textarea::make('rejected_reason')
                                ->label('Rejection Reason')
                                ->visible(fn($get) => $get('status') === Status::REJECTED->value)
                        ])
                        ->action(function (Post $record, array $data) {
                            if ($data['status'] !== Status::REJECTED->value) {
                                $data['rejected_reason'] = null;
                            }

                            if ($data['status'] === Status::PUBLISHED->value) {
                                $data['published_at'] = now();
                            }

                            $record->update($data);
                        }),

                    Tables\Actions\Action::make('View Data Changes')
                        ->label('View Data Changes')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->action(function (Post $record) {
                            // Ambil audit log terakhir dari post ini
                            $latestAudit = $record->audits()
                                ->orderBy('created_at', 'desc')
                                ->first();

                            if ($latestAudit) {
                                // Redirect ke ViewAuditLog
                                return redirect()
                                    ->to(AuditLogResource::getUrl('view', ['record' => $latestAudit]))
                                    ->with('success', 'Viewing latest audit log for this post.');
                            } else {
                                // Jika tidak ada audit log
                                Notification::make()
                                    ->title('No audit logs found')
                                    ->body('This post has no audit logs yet.')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->visible(fn(Post $record) => $record->audits()->exists()),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            'view' => Pages\ViewPost::route('/{record}'),
        ];
    }

    public static function maxContentWidth(): string
    {
        return 'full';
    }
}
