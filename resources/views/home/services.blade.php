<section id="services" class="bg-white py-16 sm:py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
        <div class="max-w-2xl sm:mx-auto sm:text-center">
            <p class="text-sm font-semibold tracking-wide text-ember-600 uppercase">Nos prestations</p>
            <h2 class="mt-3 font-display text-3xl font-bold text-stone-900 sm:text-4xl lg:text-5xl">
                Des soins sur-mesure pour votre véhicule
            </h2>
            <p class="mt-4 text-base leading-relaxed text-stone-600 sm:text-lg">
                Chaque formule est pensée pour redonner à votre voiture tout son éclat, à l'intérieur comme à l'extérieur.
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-3 sm:mt-16 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 lg:gap-8">
            @foreach (config('site.services') as $service)
                <article class="group flex gap-4 rounded-2xl border border-stone-200 bg-white p-5 transition duration-300 sm:block sm:p-8 sm:shadow-sm sm:hover:-translate-y-1 sm:hover:shadow-xl sm:hover:shadow-ember-900/5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ember-50 text-ember-600 transition duration-300 group-hover:bg-ember-600 group-hover:text-white sm:h-12 sm:w-12">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-6 w-6"
                            aria-hidden="true"
                        >{!! $service['icon'] !!}</svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-stone-900 sm:mt-6 sm:text-lg">{{ $service['title'] }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-stone-600 sm:mt-2">{{ $service['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
