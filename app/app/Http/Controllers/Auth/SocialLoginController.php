<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    private const PROVIDERS = ['google', 'apple'];

    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureProviderAllowed($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->ensureProviderAllowed($provider);

        $socialUser = Socialite::driver($provider)->stateless()->user();

        $account = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $socialUser->getId())
            ->first();

        if ($account) {
            Auth::login($account->user);
            return redirect()->intended(route('dashboard'));
        }

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'User',
                'password' => Hash::make(Str::random(12)),
            ]
        );

        $user->assignRole('user');

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_user_id' => $socialUser->getId(),
        ]);

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }

    private function ensureProviderAllowed(string $provider): void
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);
    }
}

