@php
    $contactLinks = [
        ['icon' => 'phone', 'label' => 'Téléphone', 'value' => $business['phone_display'], 'href' => $business['phone_href']],
        ['icon' => 'mail', 'label' => 'E-mail', 'value' => $business['email'], 'href' => 'mailto:'.$business['email']],
        ['icon' => 'pin', 'label' => "Zone d'intervention", 'value' => $business['area'], 'detail' => $business['area_detail']],
    ];
@endphp

<section id="contact" class="bg-stone-100 py-16 sm:py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold tracking-wide text-ember-600 uppercase">Contact</p>
            <h2 class="mt-3 font-display text-3xl font-bold text-stone-900 sm:text-4xl lg:text-5xl">Demandez votre devis gratuit</h2>
            <p class="mt-4 text-base leading-relaxed text-stone-600 sm:text-lg">
                Décrivez votre véhicule et votre besoin : nous vous répondons sous 24 h avec un devis clair, sans engagement.
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 sm:mt-12 lg:grid-cols-12 lg:gap-8">
            {{-- 1. Quote request form --}}
            <livewire:contact-form />

            {{-- 2. Contact details --}}
            <aside class="flex flex-col rounded-3xl bg-stone-900 p-5 text-white sm:p-8 lg:col-span-5 lg:p-10">
                <h3 class="text-xl font-semibold">Coordonnées</h3>
                <p class="mt-1 text-sm text-white/60">Une question rapide ? Appelez-nous directement.</p>

                <ul class="mt-6 divide-y divide-white/10">
                    @foreach ($contactLinks as $item)
                        @php($isLink = isset($item['href']))
                        <li>
                            <{{ $isLink ? 'a' : 'div' }}
                                @if ($isLink) href="{{ $item['href'] }}" @endif
                                @class([
                                    'flex items-center gap-4 py-4',
                                    'group -mx-2 rounded-xl px-2 transition hover:bg-white/5' => $isLink,
                                ])
                            >
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ember-500/15 text-ember-400">
                                    <x-icon :name="$item['icon']" stroke-width="1.6" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-medium tracking-wide text-white/50 uppercase">{{ $item['label'] }}</span>
                                    <span class="mt-0.5 block font-medium break-words">{{ $item['value'] }}</span>
                                    @isset($item['detail'])
                                        <span class="mt-0.5 block text-sm text-white/50">{{ $item['detail'] }}</span>
                                    @endisset
                                </span>
                                @if ($isLink)
                                    <x-icon name="chevron-right" class="h-5 w-5 shrink-0 text-white/30 transition group-hover:translate-x-0.5 group-hover:text-ember-400" />
                                @endif
                            </{{ $isLink ? 'a' : 'div' }}>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6 rounded-2xl bg-white/5 p-5 ring-1 ring-white/10">
                    <p class="text-xs font-medium tracking-wide text-white/50 uppercase">Horaires</p>
                    <dl class="mt-3 space-y-2 text-sm">
                        @foreach ($business['hours'] as $slot)
                            <div class="flex justify-between gap-4">
                                <dt class="text-white/70">{{ $slot['days'] }}</dt>
                                <dd class="font-medium tabular-nums">{{ $slot['time'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </aside>

            {{-- 3. Service area map --}}
            <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-stone-200 lg:col-span-12">
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div>
                        <h3 class="text-xl font-semibold text-stone-900">Zone d'intervention</h3>
                        <p class="mt-1 text-sm text-stone-500">
                            Nous venons à vous : à domicile ou sur votre lieu de travail, à Lyon et dans l'agglomération.
                        </p>
                    </div>
                    <a
                        href="{{ $business['map_link'] }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full border border-stone-300 px-5 py-2.5 text-sm font-semibold text-stone-900 transition hover:border-stone-400 hover:bg-stone-50 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ember-500"
                    >
                        Ouvrir dans Google Maps
                        <x-icon name="external" class="h-4 w-4" />
                    </a>
                </div>
                <iframe
                    title="Carte de la zone d'intervention à Lyon"
                    src="{{ $business['map_embed'] }}"
                    class="block h-72 w-full border-0 sm:h-96"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </div>
</section>
