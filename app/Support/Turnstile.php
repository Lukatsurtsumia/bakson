<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Cloudflare Turnstile, the CAPTCHA on the contact form. It is only active when both keys
 * are configured (config/services.php), so local development works without it.
 */
final class Turnstile
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public static function enabled(): bool
    {
        return filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    public static function siteKey(): ?string
    {
        return config('services.turnstile.site_key');
    }

    /**
     * Ask Cloudflare whether the widget's token is genuine. A token can only be checked once.
     */
    public static function verify(string $token, ?string $ip): bool
    {
        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(self::VERIFY_URL, [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        return $response->json('success') === true;
    }
}
