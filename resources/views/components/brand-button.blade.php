{{-- Renders a link when given an `href` (in-page CTAs, tel: links), otherwise a button. --}}
@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-2 rounded-full text-center font-semibold transition duration-200 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        [
            'sm' => 'px-4 py-2 text-sm',
            'md' => 'px-6 py-3 text-sm',
            'lg' => 'px-8 py-4 text-base',
        ][$size],
        [
            'primary' => 'bg-ember-600 text-white shadow-lg shadow-ember-900/20 hover:bg-ember-700 focus-visible:ring-ember-500',
            'outline' => 'border-2 border-white/70 text-white backdrop-blur-sm hover:bg-white/10 focus-visible:ring-white',
            'secondary' => 'border border-stone-300 bg-white text-stone-900 hover:border-stone-400 hover:bg-stone-50 focus-visible:ring-stone-400',
        ][$variant],
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
