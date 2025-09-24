<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Artesaos\SEOTools\Facades\SEOTools;

class AuthController extends Controller
{
    // Specific auth page handlers (separate blades)

    public function showLogin()
    {
        $title = 'Masuk - ' . config('app.name');
        $description = 'Masuk menggunakan email atau username ke ' . config('app.name') . '.';
        $this->setSeo($title, $description);

        return view('auth.login');
    }

    public function showRegister()
    {
        $title = 'Daftar - ' . config('app.name');
        $description = 'Buat akun baru di ' . config('app.name') . ' sebagai author untuk mulai membuat posting.';
        $this->setSeo($title, $description);

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // assign author role
        if (method_exists($user, 'assignRole')) {
            $user->assignRole(Role::AUTHOR->value);
        }

        Auth::login($user);

        // If the user was attempting to visit a protected page, return them there.
        return redirect()->intended($this->defaultRedirectUrl());
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect to intended URL (the page user attempted to access) or default by role
            return redirect()->intended($this->defaultRedirectUrl());
        }

        return back()->withErrors(['login' => 'Credentials not match'])->withInput();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    protected function redirectByRole(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        // default: author
        return redirect()->route('filament.author.pages.dashboard');
    }

    // Password reset: show form to request
    public function showForgot()
    {
        $title = 'Lupa Password - ' . config('app.name');
        $description = 'Minta link reset password untuk akun Anda di ' . config('app.name') . '.';
        $this->setSeo($title, $description);
        return view('auth.forgot');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // show reset form
    public function showReset(Request $request, $token = null)
    {
        $title = 'Reset Password - ' . config('app.name');
        $description = 'Setel ulang password akun Anda di ' . config('app.name') . '.';
        $this->setSeo($title, $description);
        return view('auth.reset', ['token' => $token]);
    }

    /**
     * Helper to set common SEO tags via SEOTools
     */
    protected function setSeo(string $title, ?string $description = null): void
    {
        SEOTools::setTitle($title);
        if ($description) {
            SEOTools::setDescription($description);
        }
        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());
        SEOTools::metatags()->addMeta(['property' => 'og:type', 'content' => 'website']);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth.show.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // Google Socialite
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName() ?? $googleUser->getNickname(),
            'username' => Str::slug($googleUser->getNickname() ?? explode('@', $googleUser->getEmail())[0]) . rand(100, 999),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(Str::random(16)),
            'avatar' => $googleUser->getAvatar(),
        ]);

        if (method_exists($user, 'assignRole') && $user->getRoleNames()->isEmpty()) {
            $user->assignRole(Role::AUTHOR->value);
        }

        Auth::login($user, true);

        // Respect the intended URL if present (user was redirected to login)
        return redirect()->intended($this->defaultRedirectUrl());
    }

    /**
     * Return the default redirect URL after authentication based on user role.
     */
    protected function defaultRedirectUrl(): string
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return route('filament.admin.pages.dashboard');
        }

        // default: author dashboard (or fallback to home if Filament route missing)
        if (Route::has('filament.author.pages.dashboard')) {
            return route('filament.author.pages.dashboard');
        }

        return route('home');
    }
}
