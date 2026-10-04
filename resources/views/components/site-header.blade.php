@php
    $navItems = [
        ['href' => '#accueil', 'label' => 'Accueil'],
        ['href' => '#services', 'label' => 'Services'],
        ['href' => '#realisations', 'label' => 'Avant / Après'],
        ['href' => '#contact', 'label' => 'Contact'],
    ];
@endphp

<header
    x-data="siteHeader"
    @scroll.window.passive="onScroll"
    @keydown.escape.window="menuOpen = false"
    class="fixed inset-x-0 top-0 z-50 border-b border-transparent transition-colors duration-300"
    :class="{ 'border-stone-200 bg-white/95 shadow-sm backdrop-blur-md': solid, 'border-transparent': !solid }"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3 sm:px-6 lg:px-8 lg:py-4">
        <a
            href="#accueil"
            class="flex items-center gap-2 font-display text-2xl font-bold tracking-tight text-white transition-colors"
            :class="{ 'text-stone-900': solid, 'text-white': !solid }"
            @click="menuOpen = false"
        >
            <svg viewBox="0 0 24 24" class="h-6 w-6 text-ember-500" aria-hidden="true">
                <path d="M12 2.5c2.5 3.5 5.5 6.8 5.5 10.2a5.5 5.5 0 1 1-11 0c0-3.4 3-6.7 5.5-10.2Z" fill="currentColor" />
            </svg>
            {{ $business['name'] }}
        </a>

        <nav class="hidden items-center gap-8 lg:flex">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    class="text-sm font-medium text-white/90 transition hover:text-ember-500"
                    :class="{ 'text-stone-700': solid, 'text-white/90': !solid }"
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-5 lg:flex">
            <a
                href="{{ $business['phone_href'] }}"
                class="text-sm font-semibold text-white tabular-nums transition hover:text-ember-500"
                :class="{ 'text-stone-900': solid, 'text-white': !solid }"
            >{{ $business['phone_display'] }}</a>
            <x-brand-button href="#contact" size="sm">Demander un devis</x-brand-button>
        </div>

        <div class="flex items-center gap-1 lg:hidden">
            <a
                href="{{ $business['phone_href'] }}"
                class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/25 backdrop-blur-sm transition"
                :class="{ 'bg-ember-50 text-ember-600 ring-0': solid, 'bg-white/10 text-white ring-1 ring-white/25 backdrop-blur-sm': !solid }"
                aria-label="Appeler le {{ $business['phone_display'] }}"
            >
                <x-icon name="phone" />
            </a>
            <button
                type="button"
                class="-mr-2 flex h-11 w-11 items-center justify-center text-white"
                :class="{ 'text-stone-900': solid, 'text-white': !solid }"
                :aria-expanded="menuOpen"
                :aria-label="menuOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
                @click="menuOpen = !menuOpen"
            >
                <x-icon name="menu" class="h-7 w-7" x-show="!menuOpen" />
                <x-icon name="close" class="h-7 w-7" x-show="menuOpen" x-cloak />
            </button>
        </div>
    </div>

    <nav
        x-show="menuOpen"
        x-cloak
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="-translate-y-2 opacity-0"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-end="-translate-y-2 opacity-0"
        class="max-h-[calc(100svh-4.25rem)] overflow-y-auto border-t border-stone-200 bg-white px-5 pt-3 pb-6 lg:hidden"
    >
        @foreach ($navItems as $item)
            <a
                href="{{ $item['href'] }}"
                class="flex items-center justify-between border-b border-stone-100 py-4 text-lg font-medium text-stone-900 active:text-ember-600"
                @click="menuOpen = false"
            >
                {{ $item['label'] }}
                <x-icon name="chevron-right" class="h-5 w-5 text-stone-300" />
            </a>
        @endforeach

        <div class="mt-6 grid grid-cols-1 gap-3">
            <x-brand-button href="#contact" size="lg" @click="menuOpen = false">Demander un devis gratuit</x-brand-button>
            <x-brand-button href="{{ $business['phone_href'] }}" variant="secondary" size="lg">
                Appeler le {{ $business['phone_display'] }}
            </x-brand-button>
        </div>
        <p class="mt-5 text-center text-sm text-stone-500">
            {{ $business['hours'][0]['days'] }} · {{ $business['hours'][0]['time'] }}
        </p>
    </nav>
</header>
