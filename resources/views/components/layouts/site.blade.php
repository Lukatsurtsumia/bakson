@props([
    'title' => 'Detailing automobile premium à Lyon',
    'description' => 'Bakson, detailing automobile premium à Lyon : lavage, nettoyage intérieur, lustrage et protection de votre véhicule, à domicile ou sur rendez-vous.',
])

@php
    $pageTitle = $title.' - '.$business['name'];
    $shareImage = \App\Support\Unsplash::url(config('site.slides.0.photo'), 1200);

    // Business details for search engines (schema.org).
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'AutomotiveBusiness',
        'name' => $business['name'],
        'description' => $description,
        'url' => url('/'),
        'image' => $shareImage,
        'telephone' => str_replace('tel:', '', $business['phone_href']),
        'email' => $business['email'],
        'areaServed' => ['@type' => 'City', 'name' => 'Lyon'],
        'openingHours' => $business['opening_hours'],
    ];
@endphp

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#0c0a09">

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $description }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="fr_FR">
        <meta property="og:site_name" content="{{ $business['name'] }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ $shareImage }}">

        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|playfair-display:600,700&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://images.unsplash.com">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="overflow-x-clip bg-white font-sans text-stone-900 antialiased">
        {{ $slot }}

        @livewireScriptConfig
    </body>
</html>
