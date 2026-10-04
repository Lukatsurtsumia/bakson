@php
    use App\Support\Unsplash;

    $slides = config('site.slides');
    // Start on a random photo so returning visitors don't always land on the same one.
    $start = random_int(0, count($slides) - 1);
    $widths = [640, 960, 1280, 1920, 2560];
    $valueProps = ['Service à domicile', 'Produits professionnels', 'Satisfaction garantie'];
@endphp

<section
    id="accueil"
    x-data="heroSlideshow({ titles: @js(array_column($slides, 'title')), start: {{ $start }} })"
    @pointerdown="onPointerDown"
    @pointerup="onPointerUp"
    @pointercancel="swipeStart = null"
    class="relative isolate h-[100svh] min-h-[36rem] touch-pan-y touch-pinch-zoom overflow-hidden bg-stone-950"
>
    <div class="absolute inset-0 -z-10">
        @foreach ($slides as $index => $slide)
            @php
                $src = Unsplash::url($slide['photo'], 960);
                $srcset = Unsplash::srcset($slide['photo'], $widths);
            @endphp
            <div
                @class(['absolute inset-0 transition-opacity duration-1000 ease-in-out', 'opacity-0' => $index !== $start])
                :class="{ 'opacity-0': active !== {{ $index }} }"
            >
                {{-- Only the first photo is in the HTML; Alpine adds the others one slide ahead, so phones don't download all of them upfront. --}}
                <img
                    @if ($index === $start)
                        src="{{ $src }}"
                        srcset="{{ $srcset }}"
                        fetchpriority="high"
                    @else
                        :src="isLoaded({{ $index }}) ? @js($src) : null"
                        :srcset="isLoaded({{ $index }}) ? @js($srcset) : null"
                    @endif
                    sizes="100vw"
                    alt="{{ $slide['title'] }}"
                    decoding="async"
                    draggable="false"
                    class="h-full w-full object-cover motion-safe:transition-transform motion-safe:duration-[7000ms] motion-safe:ease-out"
                    :class="{ 'scale-110': active === {{ $index }} }"
                >
            </div>
        @endforeach

        <div class="absolute inset-0 bg-linear-to-t from-stone-950 via-stone-950/60 to-stone-950/30"></div>
        <div class="absolute inset-0 hidden bg-linear-to-r from-stone-950/70 via-stone-950/20 to-transparent lg:block"></div>
    </div>

    <div class="mx-auto flex h-full max-w-7xl flex-col justify-end px-5 pt-24 pb-28 sm:px-6 lg:justify-center lg:px-8 lg:pb-24">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-medium text-ember-100 ring-1 ring-white/20 backdrop-blur-sm sm:text-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-ember-400"></span>
                Detailing automobile à Lyon
            </span>

            <h1 class="mt-5 font-display text-[2.6rem] leading-[1.05] font-bold text-balance text-white sm:text-6xl lg:text-7xl">
                L'éclat que votre voiture mérite.
            </h1>

            <p class="mt-5 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg">
                Nettoyage, lustrage et protection premium, à domicile ou sur rendez-vous, partout à Lyon et son agglomération.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
                <x-brand-button href="#contact" size="lg">Demander un devis gratuit</x-brand-button>
                <x-brand-button href="{{ $business['phone_href'] }}" variant="outline" size="lg">
                    <x-icon name="phone" />
                    Appeler le {{ $business['phone_display'] }}
                </x-brand-button>
            </div>

            <ul class="mt-8 hidden flex-wrap gap-x-6 gap-y-2 text-sm text-white/70 sm:flex">
                @foreach ($valueProps as $item)
                    <li class="flex items-center gap-2">
                        <x-icon name="check" class="h-4 w-4 text-ember-400" stroke-width="2.5" />
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="absolute inset-x-0 bottom-0 pb-[max(1.25rem,env(safe-area-inset-bottom))]">
        <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
            <p class="text-xs font-medium text-white/60 tabular-nums">
                <span class="text-white" x-text="pad(active + 1)">{{ sprintf('%02d', $start + 1) }}</span>
                / {{ sprintf('%02d', count($slides)) }}
                <span class="mx-1.5 text-white/30">·</span>
                <span class="text-white/80" x-text="titles[active]">{{ $slides[$start]['title'] }}</span>
            </p>
            <div class="mt-1 flex gap-1.5">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        class="group flex-1 py-3 focus:outline-hidden"
                        aria-label="Photo {{ $index + 1 }} : {{ $slide['title'] }}"
                        :aria-current="active === {{ $index }}"
                        @click="go({{ $index }})"
                    >
                        <span class="block h-1 overflow-hidden rounded-full bg-white/25 group-focus-visible:ring-2 group-focus-visible:ring-ember-400">
                            {{-- The active bar fills over the slide's duration; its animationend advances the slideshow. --}}
                            <span
                                @class(['block h-full origin-left rounded-full bg-ember-400', 'scale-x-0' => $index !== $start])
                                :class="{
                                    'animate-slide-progress': active === {{ $index }} && ! reducedMotion,
                                    'scale-x-0': active !== {{ $index }},
                                }"
                                :style="{ animationPlayState: pageHidden ? 'paused' : 'running' }"
                                @animationend="next()"
                            ></span>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>
