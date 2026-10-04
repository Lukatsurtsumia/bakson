<?php

namespace App\Support;

/**
 * URLs for Unsplash photos served from Unsplash's CDN, which resizes them and converts them to
 * WebP/AVIF on the fly.
 */
final class Unsplash
{
    public static function url(string $photoId, int $width, int $quality = 70): string
    {
        return 'https://images.unsplash.com/'.$photoId.'?'.http_build_query([
            'auto' => 'format',
            'fit' => 'max',
            'w' => $width,
            'q' => $quality,
        ]);
    }

    /**
     * @param  list<int>  $widths
     */
    public static function srcset(string $photoId, array $widths): string
    {
        return collect($widths)
            ->map(fn (int $width) => self::url($photoId, $width).' '.$width.'w')
            ->implode(', ');
    }
}
