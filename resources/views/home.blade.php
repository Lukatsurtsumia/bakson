<x-layouts.site>
    <x-site-header />

    <main>
        @include('home.hero')
        @include('home.services')
        @include('home.before-after')
        @include('home.contact')
    </main>

    <x-site-footer />

    <x-mobile-action-bar />
</x-layouts.site>
