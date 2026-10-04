<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Send browser hardening headers on every response. They live in the app rather than in the
 * proxy so they follow the code to every domain and environment.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Never let the browser guess a content type.
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Nobody else may frame the site (clickjacking on the contact form).
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // The full URL to ourselves, only the origin to others, nothing when leaving HTTPS.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // The site uses none of these, so embedded content can't ask for them either.
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()'
        );

        // Pin the browser to HTTPS for a year. Only over real TLS, so local http still works.
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // PHP adds this itself; it only advertises the version to scanners.
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
