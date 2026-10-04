{{--
    Drag-to-compare photo of one job (Alpine `beforeAfterSlider` in resources/js/components).
    On the page a tap emits `before-after-open`; in the fullscreen viewer (`reveal-on-tap`) a tap
    moves the handle instead. Without a real `before` photo, the "after" photo is dulled as a stand-in.
--}}
@props([
    'project',
    'index',
    'sizes' => '100vw',
    'revealOnTap' => false,
    'hint' => false,
])

@php
    use App\Support\Unsplash;

    $widths = [480, 768, 1080, 1600, 2200];
    $afterSrc = Unsplash::url($project['photo'], 768);
    $afterSrcset = Unsplash::srcset($project['photo'], $widths);
    $beforePhoto = $project['before'] ?? $project['photo'];
    $beforeSrc = Unsplash::url($beforePhoto, 768);
    $beforeSrcset = Unsplash::srcset($beforePhoto, $widths);
@endphp

<div
    x-data="beforeAfterSlider({ index: {{ $index }}, revealOnTap: @js($revealOnTap), hint: @js($hint) })"
    @pointerdown="onPointerDown"
    @pointermove="onPointerMove"
    @pointerup="onPointerUp"
    @pointercancel="endGesture"
    {{ $attributes->class('relative isolate cursor-ew-resize overflow-hidden bg-stone-900 select-none') }}
>
    <img
        src="{{ $afterSrc }}"
        srcset="{{ $afterSrcset }}"
        sizes="{{ $sizes }}"
        alt="{{ $project['title'] }} : après"
        loading="lazy"
        decoding="async"
        draggable="false"
        class="pointer-events-none absolute inset-0 h-full w-full object-cover"
    >

    <div
        class="pointer-events-none absolute inset-0"
        :class="{ 'transition-[clip-path] duration-500 ease-out': gliding }"
        style="clip-path: inset(0 50% 0 0)"
        :style="{ clipPath: `inset(0 ${100 - position}% 0 0)` }"
    >
        <img
            src="{{ $beforeSrc }}"
            srcset="{{ $beforeSrcset }}"
            sizes="{{ $sizes }}"
            alt="{{ $project['title'] }} : avant"
            loading="lazy"
            decoding="async"
            draggable="false"
            @class(['absolute inset-0 h-full w-full object-cover', 'before-grime' => ! $project['before']])
        >
        @unless ($project['before'])
            <div class="grime-overlay absolute inset-0"></div>
        @endunless
    </div>

    <span
        class="pointer-events-none absolute top-3 left-3 rounded-full bg-stone-950/60 px-2.5 py-1 text-[11px] font-semibold tracking-wider text-white uppercase backdrop-blur-sm transition-opacity duration-200"
        :class="{ 'opacity-0': position < 22 }"
    >Avant</span>
    <span
        class="pointer-events-none absolute top-3 right-3 rounded-full bg-ember-600/85 px-2.5 py-1 text-[11px] font-semibold tracking-wider text-white uppercase backdrop-blur-sm transition-opacity duration-200"
        :class="{ 'opacity-0': position > 78 }"
    >Après</span>

    <div
        data-handle
        role="slider"
        tabindex="0"
        aria-label="Comparer avant et après"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-valuenow="50"
        :aria-valuenow="Math.round(position)"
        :aria-valuetext="`${Math.round(position)} % de la photo avant`"
        @keydown="onHandleKeydown"
        class="group absolute inset-y-0 z-10 flex w-14 -translate-x-1/2 cursor-ew-resize touch-none items-center justify-center focus:outline-hidden"
        :class="{ 'transition-[left] duration-500 ease-out': gliding }"
        style="left: 50%"
        :style="{ left: `${position}%` }"
    >
        <span class="absolute inset-y-0 left-1/2 w-0.5 -translate-x-1/2 bg-white shadow-[0_0_10px_rgba(0,0,0,0.45)]"></span>
        <span
            class="relative flex h-11 w-11 items-center justify-center rounded-full bg-white text-stone-900 shadow-lg shadow-black/30 transition-transform duration-150 group-focus-visible:ring-4 group-focus-visible:ring-ember-400"
            :class="{ 'scale-110': dragging }"
        >
            <x-icon name="arrows-horizontal" stroke-width="2" />
        </span>
    </div>
</div>
