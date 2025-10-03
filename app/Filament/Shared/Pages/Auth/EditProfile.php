<?php

namespace App\Filament\Shared\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('avatar')
                    ->image()
                    ->avatar()
                    ->alignCenter()
                    ->directory('upload/avatars')
                    ->disk('public')
                    ->maxSize(1024),

                // Cover image for author profile header
                FileUpload::make('cover')
                    ->label('Gambar Cover')
                    ->image()
                    ->imageEditor() // allow crop/resize to fit cover
                    ->alignCenter()
                    ->directory('upload/covers')
                    ->disk('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
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

                            // Target constraints: WebP <= 250KB
                            $maxBytes = 250 * 1024;
                            $quality = 80;

                            // Encode as WebP and reduce quality until size fits
                            $encodedImage = $image->encode(new WebpEncoder(quality: $quality));
                            while ($encodedImage->size() > $maxBytes && $quality > 10) {
                                $quality -= 5;
                                $encodedImage = $image->encode(new WebpEncoder(quality: $quality));
                            }

                            // If still too large, progressively scale down width
                            if ($encodedImage->size() > $maxBytes) {
                                $resizedImage = clone $image;
                                $width = $resizedImage->width();

                                // Avoid excessive downsizing; reduce ~10% per iteration
                                while ($encodedImage->size() > $maxBytes && $width > 800) {
                                    $width = (int) floor($width * 0.9);
                                    $resizedImage = $resizedImage->scaleDown(width: $width);
                                    $encodedImage = $resizedImage->encode(new WebpEncoder(quality: $quality));
                                }
                            }

                            // Persist to disk
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
                    ->helperText('Cover disimpan sebagai WebP maksimal 250KB. Disarankan rasio sekitar 3:1 (contoh 1200×400 atau 1920×640) agar tampilan maksimal.'),

                $this->getNameFormComponent()
                    ->label('Nama Lengkap')
                    ->required()
                    ->minLength(3)
                    ->maxLength(100),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minLength(3)
                    ->maxLength(100),

                $this->getEmailFormComponent()
                    ->label('Email'),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->nullable()
                    ->string()
                    ->startsWith('62')
                    ->minLength(10)
                    ->maxLength(15),

                Select::make('sex')
                    ->label('Jenis Kelamin')
                    ->nullable()
                    ->string()
                    ->in(['male', 'female'])
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ])
                    ->placeholder('Pilih jenis kelamin'),

                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->nullable()
                    ->minDate(now()->subYears(100))
                    ->maxDate(now()->subYears(10))
                    ->placeholder('Pilih tanggal lahir'),

                Textarea::make('address')
                    ->label('Alamat')
                    ->nullable()
                    ->string()
                    ->minLength(5)
                    ->maxLength(255),

                Textarea::make('bio')
                    ->label('Bio')
                    ->rows(3),

                $this->getPasswordFormComponent()
                    ->label('Password Baru'),

                $this->getPasswordConfirmationFormComponent()
                    ->label('Konfirmasi Password Baru'),

                $this->getCurrentPasswordFormComponent()
                    ->label('Password Saat Ini'),
            ]);
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::ThreeExtraLarge;
    }
}
