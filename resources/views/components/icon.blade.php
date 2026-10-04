@props(['name'])

@php
    $paths = [
        'phone' => '<path d="M5 4h3l1.5 4-2 1.5a11 11 0 0 0 7 7l1.5-2 4 1.5v3a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z" />',
        'mail' => '<path d="M4 6.5A1.5 1.5 0 0 1 5.5 5h13A1.5 1.5 0 0 1 20 6.5v11a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 17.5v-11Z" /><path d="m4.5 6 7.5 6 7.5-6" />',
        'pin' => '<path d="M12 21s-6.5-5.6-6.5-10.5A6.5 6.5 0 1 1 18.5 10.5C18.5 15.4 12 21 12 21Z" /><circle cx="12" cy="10.5" r="2.25" />',
        'chevron-left' => '<path d="m15 6-6 6 6 6" />',
        'chevron-right' => '<path d="m9 6 6 6-6 6" />',
        'arrows-horizontal' => '<path d="m9 7-5 5 5 5M15 7l5 5-5 5" />',
        'expand' => '<path d="M14 4h6v6M10 20H4v-6M20 4l-6.5 6.5M4 20l6.5-6.5" />',
        'external' => '<path d="M7 17 17 7M8 7h9v9" />',
        'close' => '<path d="M6 18 18 6M6 6l12 12" />',
        'menu' => '<path d="M3.75 7.5h16.5M3.75 12h16.5M3.75 16.5h16.5" />',
        'check' => '<path d="m5 12.5 4.5 4.5L19 7.5" />',
        'check-circle' => '<circle cx="12" cy="12" r="8.25" /><path d="m8.5 12.25 2.5 2.5 4.5-5" />',
        'sunrise' => '<path d="M17 18a5 5 0 0 0-10 0" /><path d="M12 3v6M8.5 6.5 12 3l3.5 3.5M4.9 11.4l1.4 1.4M17.7 12.8l1.4-1.4M2 18h2M20 18h2M3 21h18" />',
        'sun' => '<circle cx="12" cy="12" r="4" /><path d="M12 2.5v2M12 19.5v2M4.6 4.6l1.4 1.4M18 18l1.4 1.4M2.5 12h2M19.5 12h2M4.6 19.4 6 18M18 6l1.4-1.4" />',
        'moon' => '<path d="M19.5 14.5A8 8 0 0 1 9.5 4.5a8 8 0 1 0 10 10Z" />',
    ];
@endphp

<svg
    {{ $attributes->merge(['class' => 'h-5 w-5', 'stroke-width' => '1.75']) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
>{!! $paths[$name] !!}</svg>
