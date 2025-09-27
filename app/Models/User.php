<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Role;
use App\Traits\Auditable;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements MustVerifyEmail, HasAvatar, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Auditable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['avatar_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function postComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function avatarUrl(): Attribute
    {
        $url = '';

        if ($this->avatar == null) {
            $url = 'https://ui-avatars.com/api/?name=' . Str::substr($this->name, 0, 1) . '&color=FFFFFF&background=oklch(0.141%200.005%20285.823)';
        } else if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            $url = $this->avatar;
        } else {
            $url = asset('storage/' . $this->avatar);
        }

        return Attribute::make(
            get: fn() => $url,
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function isAdmin(): bool
    {
        return $this->getRoleNames()->first() === Role::ADMIN->value;
    }

    public function isAuthor(): bool
    {
        return $this->getRoleNames()->first() === Role::AUTHOR->value;
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function threadComments(): HasMany
    {
        return $this->hasMany(ThreadComment::class);
    }
}
