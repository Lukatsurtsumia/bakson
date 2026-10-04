{{-- Phone-only "Appeler / Devis" bar: it appears once the hero is scrolled past and steps aside
     while the contact section is on screen, so it never covers the form. --}}
<div
    x-data="mobileActionBar"
    x-show="visible"
    x-cloak
    x-transition:enter="transition duration-300 ease-out"
    x-transition:enter-start="translate-y-full"
    x-transition:leave="transition duration-200 ease-in"
    x-transition:leave-end="translate-y-full"
    @scroll.window.passive="onScroll"
    class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white/95 px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] shadow-[0_-8px_24px_-12px_rgba(0,0,0,0.15)] backdrop-blur-md lg:hidden"
>
    <div class="grid grid-cols-2 gap-3">
        <x-brand-button href="{{ $business['phone_href'] }}" variant="secondary">
            <x-icon name="phone" />
            Appeler
        </x-brand-button>
        <x-brand-button href="#contact">Devis gratuit</x-brand-button>
    </div>
</div>
