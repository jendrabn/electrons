<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();

        $user = User::firstOrCreate([
            'email' => $user->email
        ], [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => bcrypt(str()->random(8)),
            'google_id' => $user->getId(),
            'avatar' => $user->getAvatar(),
        ]);

        // assign role [author] if role not assigned
        if ($user->getRoleNames()->first() !== 'admin') {
            $user->assignRole('author');
        }

        auth()->login($user);

        if ($user->getRoleNames()->first() === 'admin') {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        } else if ($user->getRoleNames()->first() === 'author') {
            return redirect()->to(Dashboard::getUrl(panel: 'author'));
        } else {
            abort(404);
        }
    }
}
