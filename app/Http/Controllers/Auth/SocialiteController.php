<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        if (! config('services.google.client_id')) {
            return redirect('/app/login')->withErrors([
                'email' => 'Google sign-in is not configured yet.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect('/app/login')->withErrors([
                'email' => 'Google sign-in failed. Please try again or use email and password.',
            ]);
        }

        $user = User::where('email', $google->getEmail())->first();

        if (! $user || ! $user->is_active) {
            return redirect('/app/login')->withErrors([
                'email' => 'No active account found for this Google email. Contact HR.',
            ]);
        }

        if (! $user->google_id) {
            $user->forceFill([
                'google_id' => $google->getId(),
                'provider'  => 'google',
            ])->save();
        }

        Auth::login($user, remember: true);
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect('/app');
    }
}
