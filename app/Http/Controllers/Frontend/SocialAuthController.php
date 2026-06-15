<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomUser;
use App\Traits\MergesGuestCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use MergesGuestCart;

    /** Providers we allow. */
    private array $allowed = ['google', 'facebook'];

    /**
     * Step 1: send the user off to Google / Facebook.
     */
    public function redirectToProvider(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed), 404);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Step 2: provider sends the user back here.
     */
    public function handleProviderCallback(string $provider)
    {
        abort_unless(in_array($provider, $this->allowed), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::error('[Social] ' . $provider . ' login failed: ' . $e->getMessage());

            return redirect()->route('show.checkout')
                ->with('error', 'Social login failed. Please try again.');
        }

        $email = $socialUser->getEmail();

        // Google always returns email; Facebook needs the "email" permission granted.
        if (!$email) {
            return redirect()->route('show.checkout')
                ->with('error', ucfirst($provider) . ' did not share an email. Please use OTP signup instead.');
        }

        // ★ Capture guest session id BEFORE login (login regenerates the session).
        $guestSessionId = session()->getId();

        // Find an existing account by email (covers OTP-created accounts too),
        // otherwise create a fresh one.
        $user = CustomUser::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $socialUser->getName() ?? '',
                'password' => bcrypt(Str::random(32)),
            ]
        );

        // Fill name only if it was empty (don't overwrite an existing saved name).
        if (empty($user->name) && $socialUser->getName()) {
            $user->name = $socialUser->getName();
        }

        // Save / refresh provider info.
        $user->forceFill([
            'provider'    => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar'      => $socialUser->getAvatar(),
        ])->save();

        Auth::guard('custom')->login($user, true);

        // ★ Merge guest cart into this user's cart.
        $this->mergeGuestCart($user->id, $guestSessionId);

        return redirect()->route('show.checkout');
    }
}