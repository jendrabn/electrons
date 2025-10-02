<?php

namespace App\Filament\Admin\Resources\Users;

use App\Enums\Role;
use App\Filament\Admin\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(UserFormSchema::getSchema());
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Basic Information
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name')
                    ->label('Nama'),
                TextEntry::make('username')
                    ->label('USERNAME')
                    ->placeholder('Belum diisi'),
                TextEntry::make('email')
                    ->label('Email')
                    ->copyable(),

                // Personal Information
                TextEntry::make('sex')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                        default => 'Tidak ditentukan',
                    }),
                TextEntry::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('F j, Y')
                    ->placeholder('Belum diisi'),
                TextEntry::make('phone')
                    ->label('Nomor Telepon')
                    ->placeholder('Tidak tersedia')
                    ->copyable(),
                TextEntry::make('address')
                    ->label('Alamat')
                    ->placeholder('Tidak tersedia'),

                // Role & Statistics
                TextEntry::make('role')
                    ->label('Peran')
                    ->badge()
                    ->colors([
                        'primary' => Role::ADMIN->value,
                        'success' => Role::AUTHOR->value,
                    ])
                    ->getStateUsing(fn (User $record): string => $record->getRoleNames()->first()),
                TextEntry::make('posts_count')
                    ->label('Total Blog Post')
                    ->numeric()
                    ->suffix(' postingan')
                    ->color('info'),

                // Account Status
                TextEntry::make('is_suspended')
                    ->label('Status Akun')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ditangguhkan' : 'Aktif')
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success'),
                TextEntry::make('suspend_reason')
                    ->label('Alasan Penangguhan')
                    ->placeholder('Tanpa alasan')
                    ->visible(fn ($record): bool => $record?->is_suspended ?? false),

                // Verification
                TextEntry::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'Ya' : 'Tidak')
                    ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
                TextEntry::make('google_id')
                    ->label('Akun Google')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'Terhubung' : 'Tidak Terhubung')
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray'),

                // Timestamps
                TextEntry::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('M j, Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('M j, Y H:i'),
                TextEntry::make('suspended_at')
                    ->label('Ditangguhkan Pada')
                    ->dateTime('M j, Y H:i')
                    ->visible(fn ($record): bool => $record?->suspended_at !== null),
                TextEntry::make('unsuspended_at')
                    ->label('Penangguhan Dicabut Pada')
                    ->dateTime('M j, Y H:i')
                    ->visible(fn ($record): bool => $record?->unsuspended_at !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        $modalConfig = UserFormSchema::getModalConfig();

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')

                    ->sortable()
                    ->searchable(),
                ImageColumn::make('avatar_url')
                    ->label('AVATAR')
                    ->imageSize(40)
                    ->extraImgAttributes([
                        'style' => 'object-fit: cover; border: 1px solid #fbbf24; padding: 1px; border-radius: 4px;',
                    ]),
                TextColumn::make('name')
                    ->label('NAMA')

                    ->sortable()
                    ->searchable(),
                TextColumn::make('username')
                    ->label('USERNAME')

                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('EMAIL')

                    ->sortable()
                    ->searchable(),
                TextColumn::make('role')
                    ->label('PERAN')

                    ->badge()
                    ->colors([
                        'primary' => Role::ADMIN->value,
                        'success' => Role::AUTHOR->value,
                    ])
                    ->getStateUsing(fn (User $record): string => $record->getRoleNames()->first())
                    ->searchable(),
                TextColumn::make('posts_count')
                    ->label('JUMLAH BLOG POST')

                    ->counts('posts')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('TELEPON')

                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sex')
                    ->label('JENIS KELAMIN')

                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')
                    ->label('TANGGAL LAHIR')

                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('address')
                    ->label('ALAMAT')

                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_suspended')
                    ->label('STATUS')

                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->colors([
                        'danger' => '0',
                        'success' => '1',
                    ])
                    ->getStateUsing(fn (User $record): string => $record->is_suspended ? 'Ditangguhkan' : 'Aktif')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->label('TANGGAL & WAKTU VERIFIKASI')

                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('TANGGAL & WAKTU DIBUAT')

                    ->dateTime('d M Y, H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i:s')
                    ->label('TANGGAL & WAKTU DIPERBARUI')

                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Peran')
                    ->relationship('roles', 'name')
                    ->options(Role::class),
                SelectFilter::make('sex')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ]),
                SelectFilter::make('is_suspended')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Ditangguhkan',
                    ]),
                SelectFilter::make('is_verified')
                    ->label('Terverifikasi')
                    ->options([
                        'verified' => 'Terverifikasi',
                        'unverified' => 'Belum Terverifikasi',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalWidth($modalConfig['width'])
                    ->modalHeading('Detail Pengguna')
                    ->modalAlignment($modalConfig['alignment'])
                    ->closeModalByClickingAway(false),
                EditAction::make()
                    ->modalWidth($modalConfig['width'])
                    ->modalHeading('Ubah Pengguna')
                    ->modalAlignment($modalConfig['alignment'])
                    ->closeModalByClickingAway(false)
                    ->schema(UserFormSchema::getSchema())
                    ->mutateDataUsing(function ($data) use (&$tempRole) {
                        $tempRole = $data['role'] ?? null;
                        unset($data['role']);

                        if (isset($data['password'])) {
                            $data['password'] = bcrypt($data['password']);
                        } else {
                            unset($data['password']);
                        }

                        return $data;
                    })
                    ->after(function (User $record, array $data) use (&$tempRole) {
                        $record->syncRoles([$tempRole]);
                    })
                    ->successNotificationTitle('Pengguna berhasil diperbarui'),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotificationTitle('Pengguna berhasil dihapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->successNotificationTitle('Pengguna berhasil dihapus'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
