@php
    $navItems = [
        ['href' => '#accueil', 'label' => 'Accueil'],
        ['href' => '#services', 'label' => 'Services'],
        ['href' => '#realisations', 'label' => 'Avant / Après'],
        ['href' => '#contact', 'label' => 'Contact'],
    ];
@endphp

<footer class="bg-stone-950 text-stone-300">
    {{-- The extra bottom padding on phones keeps the last lines clear of the mobile action bar. --}}
    <div class="mx-auto max-w-7xl px-5 pt-14 pb-28 sm:px-6 lg:px-8 lg:pt-16 lg:pb-16">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="flex items-center gap-2 font-display text-xl font-bold text-white">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-ember-500" aria-hidden="true">
                        <path d="M12 2.5c2.5 3.5 5.5 6.8 5.5 10.2a5.5 5.5 0 1 1-11 0c0-3.4 3-6.7 5.5-10.2Z" fill="currentColor" />
                    </svg>
                    {{ $business['name'] }}
                </p>
                <p class="mt-4 text-sm leading-relaxed text-stone-400">
                    Detailing automobile premium à Lyon. Un soin minutieux pour chaque véhicule, à domicile ou sur rendez-vous.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">Navigation</h3>
                <ul class="mt-4 space-y-3 text-sm">
                    @foreach ($navItems as $item)
                        <li>
                            <a href="{{ $item['href'] }}" class="text-stone-400 transition hover:text-ember-400">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">Contact</h3>
                <ul class="mt-4 space-y-3 text-sm text-stone-400">
                    <li>
                        <a href="{{ $business['phone_href'] }}" class="transition hover:text-ember-400">{{ $business['phone_display'] }}</a>
                    </li>
                    <li>
                        <a href="mailto:{{ $business['email'] }}" class="break-words transition hover:text-ember-400">{{ $business['email'] }}</a>
                    </li>
                    <li>{{ $business['area'] }}</li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">Horaires</h3>
                <ul class="mt-4 space-y-3 text-sm text-stone-400">
                    @foreach ($business['hours'] as $slot)
                        <li>{{ $slot['days'] }} : {{ $slot['time'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-2 border-t border-stone-800 pt-8 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $business['name'] }}. Tous droits réservés.</p>
            <p>Detailing automobile &middot; Lyon</p>
        </div>
    </div>
</footer>
