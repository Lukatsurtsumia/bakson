@php
    $projects = config('site.projects');
@endphp

<section
    id="realisations"
    x-data="beforeAfterGallery({ titles: @js(array_column($projects, 'title')), services: @js(array_column($projects, 'service')) })"
    @before-after-open="openViewer($event.detail.index)"
    @resize.window.passive="onTrackScroll"
    class="overflow-hidden bg-stone-950 py-16 text-white sm:py-24 lg:py-32"
>
    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-6">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold tracking-wide text-ember-400 uppercase">Nos réalisations</p>
                <h2 class="mt-3 font-display text-3xl font-bold sm:text-4xl lg:text-5xl">Avant / Après</h2>
                <p class="mt-4 text-base leading-relaxed text-white/65 sm:text-lg">
                    Faites glisser la poignée pour voir la différence. Touchez une photo pour l'ouvrir en plein écran.
                </p>
            </div>

            <div class="hidden shrink-0 gap-2 sm:flex">
                <button
                    type="button"
                    class="flex h-12 w-12 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ember-400 disabled:opacity-30"
                    aria-label="Réalisations précédentes"
                    disabled
                    :disabled="atStart"
                    @click="scrollByCard(-1)"
                >
                    <x-icon name="chevron-left" />
                </button>
                <button
                    type="button"
                    class="flex h-12 w-12 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ember-400 disabled:opacity-30"
                    aria-label="Réalisations suivantes"
                    :disabled="atEnd"
                    @click="scrollByCard(1)"
                >
                    <x-icon name="chevron-right" />
                </button>
            </div>
        </div>
    </div>

    <div
        x-ref="track"
        @scroll.passive="onTrackScroll"
        class="mt-10 flex snap-x snap-mandatory scroll-px-5 gap-4 overflow-x-auto px-5 pb-2 scrollbar-none sm:mt-12 sm:scroll-px-6 sm:gap-6 sm:px-6 lg:scroll-px-[max(2rem,calc((100vw-80rem)/2+2rem))] lg:px-[max(2rem,calc((100vw-80rem)/2+2rem))]"
    >
        @foreach ($projects as $index => $project)
            <article class="w-[84%] shrink-0 snap-start sm:w-[58%] lg:w-[calc((100%-3rem)/3)]">
                <div class="relative overflow-hidden rounded-2xl ring-1 ring-white/10">
                    <x-before-after-slider
                        :project="$project"
                        :index="$index"
                        sizes="(min-width: 1024px) 400px, (min-width: 640px) 58vw, 84vw"
                        :hint="$index === 0"
                        class="aspect-[4/5] sm:aspect-[4/3]"
                    />
                    <button
                        type="button"
                        class="absolute right-3 bottom-3 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-stone-950/60 text-white backdrop-blur-sm transition hover:bg-stone-950/80 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ember-400"
                        aria-label="Agrandir : {{ $project['title'] }}"
                        @click="openViewer({{ $index }})"
                    >
                        <x-icon name="expand" />
                    </button>
                </div>

                <div class="mt-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-wider text-ember-400 uppercase">{{ $project['service'] }}</p>
                        <h3 class="mt-1 text-lg font-semibold">{{ $project['title'] }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-white/60">{{ $project['description'] }}</p>
                    </div>
                    <span class="font-display text-2xl font-bold text-white/15 tabular-nums">{{ sprintf('%02d', $index + 1) }}</span>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mx-auto mt-8 flex max-w-7xl items-center gap-4 px-5 sm:px-6 lg:px-8">
        <span class="text-sm text-white/60 tabular-nums">
            <span class="font-semibold text-white" x-text="pad(activeIndex + 1)">01</span>
            / {{ sprintf('%02d', count($projects)) }}
        </span>
        <div class="h-0.5 flex-1 overflow-hidden rounded-full bg-white/15">
            <div
                class="h-full origin-left rounded-full bg-ember-400 transition-transform duration-200"
                :style="{ transform: `scaleX(${scrollProgress})` }"
            ></div>
        </div>
    </div>

    {{-- Fullscreen viewer. The phone's back button closes it (see openViewer in the Alpine component). --}}
    <div
        x-show="viewerOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-[100] flex flex-col bg-stone-950 text-white"
        role="dialog"
        aria-modal="true"
        aria-label="Avant / après en plein écran"
    >
        <header class="flex items-center justify-between gap-4 px-4 pt-[max(0.75rem,env(safe-area-inset-top))] pb-3 sm:px-6">
            <p class="text-sm font-medium text-white/60 tabular-nums">
                <span class="text-white" x-text="pad(viewerIndex + 1)"></span>
                / {{ sprintf('%02d', count($projects)) }}
            </p>
            <button
                type="button"
                x-ref="closeButton"
                class="-mr-2 flex h-11 w-11 items-center justify-center rounded-full transition hover:bg-white/10 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ember-400"
                aria-label="Fermer"
                @click="closeViewer"
            >
                <x-icon name="close" class="h-6 w-6" />
            </button>
        </header>

        <div
            x-ref="viewerTrack"
            @scroll.passive="onViewerScroll"
            class="flex min-h-0 flex-1 snap-x snap-mandatory overflow-x-auto overscroll-x-contain scrollbar-none"
        >
            @foreach ($projects as $index => $project)
                <div class="flex h-full w-full shrink-0 snap-center snap-always items-center justify-center sm:px-16">
                    <x-before-after-slider
                        :project="$project"
                        :index="$index"
                        sizes="(min-width: 640px) 1024px, 100vw"
                        reveal-on-tap
                        class="aspect-[4/5] max-h-full w-full sm:aspect-[3/2] sm:max-w-5xl sm:rounded-xl"
                    />
                </div>
            @endforeach
        </div>

        <footer class="px-4 pt-4 pb-[max(1rem,env(safe-area-inset-bottom))] sm:px-6">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-semibold tracking-wider text-ember-400 uppercase" x-text="services[viewerIndex]"></p>
                    <p class="mt-0.5 truncate font-display text-lg font-semibold" x-text="titles[viewerIndex]"></p>
                </div>
                <div class="flex shrink-0 gap-2">
                    <button
                        type="button"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ember-400 disabled:opacity-30"
                        aria-label="Réalisation précédente"
                        :disabled="viewerIndex === 0"
                        @click="viewerGoTo(viewerIndex - 1)"
                    >
                        <x-icon name="chevron-left" />
                    </button>
                    <button
                        type="button"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white/10 focus:outline-hidden focus-visible:ring-2 focus-visible:ring-ember-400 disabled:opacity-30"
                        aria-label="Réalisation suivante"
                        :disabled="viewerIndex === count - 1"
                        @click="viewerGoTo(viewerIndex + 1)"
                    >
                        <x-icon name="chevron-right" />
                    </button>
                </div>
            </div>
            <p class="mt-3 text-center text-xs text-white/45">
                Glissez la poignée pour comparer · balayez pour changer de réalisation
            </p>
        </footer>
    </div>
</section>
