<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function authWithGoogle(Request $request)
    {
        return Socialite::driver('google')->redirect();
    }

    public function authWithGoogleCallback(Request $request)
    {
        $user = Socialite::driver('google')->user();

        $user = User::firstOrCreate([
            'email' => $user->getEmail(),
        ], [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => bcrypt(str()->random(8)),
            'google_id' => $user->getId(),
            'avatar' => $user->getAvatar(),
        ]);

        if (! $user->hasRole(Role::AUTHOR->value)) {
            $user->assignRole(Role::AUTHOR->value);
        }

        auth()->login($user);

        if ($user->hasRole(Role::ADMIN->value)) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        } else if ($user->hasRole(Role::AUTHOR->value)) {
            return redirect()->to(Dashboard::getUrl(panel: 'author'));
        } else {
            return abort(403);
        }
    }
}
