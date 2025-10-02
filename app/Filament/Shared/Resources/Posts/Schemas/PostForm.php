<?php

namespace App\Filament\Shared\Resources\Posts\Schemas;

use App\Enums\Status;
use App\Models\Category;
use App\Services\AIContentGeneratorService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\Actions\AttachFilesAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('rejected_reason')
                    ->label('Alasan Penolakan')
                    ->state(fn($record) => $record->rejected_reason ?? '-')
                    ->visible(fn($record) => $record?->status === Status::REJECTED->value)
                    ->color('danger')
                    ->inlineLabel()
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->label('Kategori')
                    ->required()
                    ->options(Category::all()->pluck('name', 'id'))
                    ->hintIcon('heroicon-o-information-circle', 'Pilih kategori yang sesuai dengan topik konten')
                    ->inlineLabel()
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->string()
                    ->minLength(3)
                    ->maxLength(100)
                    ->hintIcon('heroicon-o-information-circle', 'Buat judul yang jelas dan mewakili topik konten (disarankan tidak lebih dari delapan kata)')
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-3xl']),
                FileUpload::make('image')
                    ->label('Gambar Latar')
                    ->image()
                    ->directory('uploads')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->getUploadedFileNameForStorageUsing(function (FileUpload $component, TemporaryUploadedFile $file): string {
                        if ($component->shouldPreserveFilenames()) {
                            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                            return Str::slug($name) . '.webp';
                        }

                        return Str::ulid() . '.webp';
                    })
                    ->saveUploadedFileUsing(function (FileUpload $component, TemporaryUploadedFile $file): ?string {
                        try {
                            if (! $file->exists()) {
                                return null;
                            }
                            $manager = new ImageManager(new GdDriver);
                            $image = $manager->read($file->getRealPath());
                            $maxBytes = 250 * 1024;
                            $quality = 80;
                            $encodedImage = $image->encode(new WebpEncoder(quality: $quality));
                            while ($encodedImage->size() > $maxBytes && $quality > 10) {
                                $quality -= 5;
                                $encodedImage = $image->encode(new WebpEncoder(quality: $quality));
                            }
                            if ($encodedImage->size() > $maxBytes) {
                                $resizedImage = clone $image;
                                $width = $resizedImage->width();
                                while ($encodedImage->size() > $maxBytes && $width > 600) {
                                    $width = (int) floor($width * 0.9);
                                    $resizedImage = $resizedImage->scaleDown(width: $width);
                                    $encodedImage = $resizedImage->encode(new WebpEncoder(quality: $quality));
                                }
                            }
                            $directory = trim((string) $component->getDirectory(), '/');
                            $fileName = $component->getUploadedFileNameForStorage($file);
                            $path = $directory !== '' ? "{$directory}/{$fileName}" : $fileName;
                            $disk = $component->getDisk();
                            $visibility = $component->getVisibility();
                            $options = [];
                            if ($visibility === 'public') {
                                $options['visibility'] = $visibility;
                            }
                            $disk->put($path, $encodedImage->toString(), $options);

                            return $path;
                        } catch (Throwable $exception) {
                            report($exception);

                            return null;
                        }
                    })
                    ->maxSize(5120) // 5MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Unggah gambar dalam format JPEG, PNG, atau WebP. Maksimal 5MB.')
                    ->hintIcon('heroicon-o-information-circle', 'Unggah gambar dalam format JPEG, PNG, atau WebP. Maksimal 5MB.')
                    ->inlineLabel()
                    ->columnSpanFull(),
                TextInput::make('image_caption')
                    ->label('Keterangan Gambar')
                    ->hintIcon('heroicon-o-information-circle', 'Tambahkan keterangan gambar untuk membantu pembaca memahami gambar.')
                    ->inlineLabel()
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->label('Konten')
                    ->required()
                    ->hintActions([
                        Action::make('generateWithAI')
                            ->label('🤖 Generate dengan AI')
                            ->color('info')
                            ->icon('heroicon-o-sparkles')
                            ->visible(fn() => app(AIContentGeneratorService::class)->canAccessAI())
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('topic')
                                            ->label('Topik/Judul Artikel')
                                            ->required()
                                            ->placeholder('Contoh: Cara Membuat REST API dengan Laravel')
                                            ->columnSpanFull(),
                                        Select::make('ai_category')
                                            ->label('Kategori')
                                            ->options(Category::all()->pluck('name', 'name'))
                                            ->searchable()
                                            ->preload(),
                                        Select::make('difficulty')
                                            ->label('Tingkat Kesulitan')
                                            ->options([
                                                'Pemula' => 'Pemula',
                                                'Menengah' => 'Menengah',
                                                'Lanjutan' => 'Lanjutan',
                                            ])
                                            ->default('Pemula'),
                                        Select::make('target_audience')
                                            ->label('Target Pembaca')
                                            ->options([
                                                'Developer Pemula' => 'Developer Pemula',
                                                'Junior Developer' => 'Junior Developer',
                                                'Senior Developer' => 'Senior Developer',
                                                'Mahasiswa IT' => 'Mahasiswa IT',
                                                'Umum' => 'Umum',
                                            ])
                                            ->default('Developer Pemula'),
                                        Select::make('programming_language')
                                            ->label('Bahasa Pemrograman')
                                            ->options([
                                                'PHP' => 'PHP',
                                                'JavaScript' => 'JavaScript',
                                                'Python' => 'Python',
                                                'Java' => 'Java',
                                                'C#' => 'C#',
                                                'Go' => 'Go',
                                                'TypeScript' => 'TypeScript',
                                                'HTML/CSS' => 'HTML/CSS',
                                                'SQL' => 'SQL',
                                                'Lainnya' => 'Lainnya',
                                            ])
                                            ->searchable(),
                                        TextInput::make('framework_tools')
                                            ->label('Framework/Tools')
                                            ->placeholder('Contoh: Laravel, React, Vue.js, Bootstrap')
                                            ->columnSpanFull(),
                                        Select::make('article_length')
                                            ->label('Panjang Artikel')
                                            ->options([
                                                'Pendek (500-800 kata)' => 'Pendek (500-800 kata)',
                                                'Sedang (800-1500 kata)' => 'Sedang (800-1500 kata)',
                                                'Panjang (1500+ kata)' => 'Panjang (1500+ kata)',
                                            ])
                                            ->default('Sedang (800-1500 kata)')
                                            ->columnSpanFull(),
                                        Textarea::make('key_points')
                                            ->label('Poin-poin Kunci (Opsional)')
                                            ->placeholder('Contoh:&#10;- Penjelasan tentang routing&#10;- Implementasi middleware&#10;- Contoh testing API')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Textarea::make('additional_requirements')
                                            ->label('Persyaratan Tambahan (Opsional)')
                                            ->placeholder('Contoh: Sertakan contoh kode untuk CRUD, jelaskan tentang validasi, tambahkan tips security')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->action(function (array $data, Set $set) {
                                try {
                                    $aiService = app(AIContentGeneratorService::class);
                                    $result = $aiService->generateContent($data);

                                    if ($result['success']) {
                                        $set('content', $result['content']);

                                        Notification::make()
                                            ->title('Konten berhasil digenerate!')
                                            ->body('Konten AI telah dimasukkan ke editor. Anda dapat mengedit sesuai kebutuhan.')
                                            ->success()
                                            ->send();
                                    } else {
                                        Notification::make()
                                            ->title('Error Generate Konten')
                                            ->body($result['error'])
                                            ->danger()
                                            ->send();
                                    }
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Terjadi kesalahan: ' . $e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->modalHeading('Generate Konten dengan AI')
                            ->modalDescription('Isi form berikut untuk menghasilkan konten artikel tutorial coding yang berkualitas menggunakan AI.')
                            ->modalSubmitActionLabel('Generate Konten')
                            ->modalWidth('4xl'),
                        Action::make('generateTitle')
                            ->label('💡 Saran Judul')
                            ->color('warning')
                            ->icon('heroicon-o-light-bulb')
                            ->visible(fn() => app(AIContentGeneratorService::class)->canAccessAI())
                            ->schema([
                                TextInput::make('topic_for_title')
                                    ->label('Topik Artikel')
                                    ->required()
                                    ->placeholder('Contoh: Laravel API Authentication'),
                                Select::make('category_for_title')
                                    ->label('Kategori')
                                    ->options(Category::all()->pluck('name', 'name'))
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->action(function (array $data, Set $set) {
                                try {
                                    $aiService = app(AIContentGeneratorService::class);
                                    $result = $aiService->generateTitleSuggestions(
                                        $data['topic_for_title'],
                                        $data['category_for_title'] ?? ''
                                    );

                                    if ($result['success']) {
                                        Notification::make()
                                            ->title('Saran Judul')
                                            ->body($result['suggestions'])
                                            ->info()
                                            ->persistent()
                                            ->send();
                                    } else {
                                        Notification::make()
                                            ->title('Error Generate Judul')
                                            ->body($result['error'])
                                            ->danger()
                                            ->send();
                                    }
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Terjadi kesalahan: ' . $e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->modalHeading('Generate Saran Judul')
                            ->modalSubmitActionLabel('Generate Saran'),
                    ])
                    ->toolbarButtons(
                        [
                            [
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'subscript',
                                'superscript',
                                'link',
                            ],
                            [
                                'textColor',
                                'h1',
                                'h2',
                                'h3',
                                'blockquote',
                                'code',
                                'codeBlock',
                            ],
                            [
                                'bulletList',
                                'orderedList',
                                'table',
                                'tableAddColumnBefore',
                                'tableAddColumnAfter',
                                'tableDeleteColumn',
                            ],
                            [
                                'tableAddRowBefore',
                                'tableAddRowAfter',
                                'tableDeleteRow',
                                'tableMergeCells',
                                'tableSplitCell',
                                'tableToggleHeaderRow',
                                'tableDelete',
                            ],
                            [
                                'attachFiles',
                                'customBlocks',
                                'mergeTags',
                                'horizontalRule',
                                'highlight',
                                'small',
                                'lead',
                            ],
                            [
                                'undo',
                                'redo',
                                'alignStart',
                                'alignCenter',
                                'alignEnd',
                                'alignJustify',
                                'grid',
                            ],
                            [
                                'gridDelete',
                                'details',
                                'clearFormatting',
                                'fullscreen',
                            ],
                        ]
                    )
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('uploads/post_contents')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->fileAttachmentsMaxSize(5120)
                    ->registerActions([
                        fn(RichEditor $component) => AttachFilesAction::make()
                            ->schema(fn(array $arguments, RichEditor $component): array => [
                                FileUpload::make('file')
                                    ->label(filled($arguments['src'] ?? null)
                                        ? __('filament-forms::components.rich_editor.actions.attach_files.modal.form.file.label.existing')
                                        : __('filament-forms::components.rich_editor.actions.attach_files.modal.form.file.label.new'))
                                    ->acceptedFileTypes($component->getFileAttachmentsAcceptedFileTypes())
                                    ->maxSize($component->getFileAttachmentsMaxSize())
                                    ->storeFiles(false)
                                    ->required(blank($arguments['src'] ?? null))
                                    ->hiddenLabel(blank($arguments['src'] ?? null))
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080'),
                                TextInput::make('alt')
                                    ->label(filled($arguments['src'] ?? null)
                                        ? __('filament-forms::components.rich_editor.actions.attach_files.modal.form.alt.label.existing')
                                        : __('filament-forms::components.rich_editor.actions.attach_files.modal.form.alt.label.new'))
                                    ->maxLength(1000),
                            ]),
                    ])
                    ->extraInputAttributes(['style' => 'min-height: 360px;', 'class' => 're-wrap  re-compact'], merge: true)
                    ->saveUploadedFileAttachmentUsing(function (TemporaryUploadedFile $file, RichEditor $component): ?string {
                        try {
                            if (! $file->exists()) {
                                return null;
                            }

                            $manager = new ImageManager(new GdDriver);
                            $image = $manager->read($file->getRealPath());
                            $maxBytes = 250 * 1024;
                            $quality = 80;
                            $encodedImage = $image->encode(new WebpEncoder(quality: $quality));

                            while ($encodedImage->size() > $maxBytes && $quality > 10) {
                                $quality -= 5;
                                $encodedImage = $image->encode(new WebpEncoder(quality: $quality));
                            }

                            if ($encodedImage->size() > $maxBytes) {
                                $resizedImage = clone $image;
                                $width = $resizedImage->width();

                                while ($encodedImage->size() > $maxBytes && $width > 600) {
                                    $width = (int) floor($width * 0.9);
                                    $resizedImage = $resizedImage->scaleDown(width: $width);
                                    $encodedImage = $resizedImage->encode(new WebpEncoder(quality: $quality));
                                }
                            }

                            $directory = trim((string) $component->getFileAttachmentsDirectory(), '/');
                            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                            $fileName = Str::slug($baseName);

                            if ($fileName === '') {
                                $fileName = (string) Str::ulid();
                            }

                            $fileName .= '.webp';
                            $path = $directory !== '' ? "{$directory}/{$fileName}" : $fileName;

                            $disk = $component->getFileAttachmentsDisk();
                            $visibility = $component->getFileAttachmentsVisibility();
                            $options = [];

                            if ($visibility === 'public') {
                                $options['visibility'] = $visibility;
                            }

                            $disk->put($path, $encodedImage->toString(), $options);

                            return $path;
                        } catch (Throwable $exception) {
                            report($exception);

                            return null;
                        }
                    })
                    ->hintIcon('heroicon-o-information-circle', 'Tulis konten dengan jelas dan baik')
                    ->columnSpanFull(),
                TextInput::make('teaser')
                    ->label('Ringkasan')
                    ->nullable()
                    ->minLength(3)
                    ->maxLength(150)
                    ->hintIcon('heroicon-o-information-circle', 'Buat ringkasan atau inti topik konten agar mudah ditemukan di mesin pencari.')
                    ->inlineLabel()
                    ->columnSpanFull(),
                Select::make('tags')
                    ->label('Tag')
                    ->relationship(name: 'tags', titleAttribute: 'name', modifyQueryUsing: fn($query) => $query->orderBy('name', 'asc'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->hintIcon('heroicon-o-information-circle', 'Tambahkan Tag (kata kunci) yang berkaitan dengan konten. Gunakan tombol Rekomendasi untuk mendapatkan saran Tag otomatis.')
                    ->inlineLabel()
                    ->columnSpanFull(),
            ]);
    }
}
