@php
    // Only one set of border colours at a time, so an invalid field reliably turns red.
    $inputClass = fn (string $field) => 'mt-1.5 block w-full rounded-xl bg-white px-4 py-3 text-base text-stone-900 shadow-xs placeholder:text-stone-400 '
        .($errors->has($field) ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-stone-300 focus:border-ember-500 focus:ring-ember-500');
@endphp

<div id="devis" class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-stone-200 sm:p-8 lg:col-span-7 lg:p-10">
    @if ($sent)
        <div class="flex h-full flex-col items-center justify-center py-10 text-center" role="status">
            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-ember-50 text-ember-600">
                <x-icon name="check-circle" class="h-9 w-9" stroke-width="1.5" />
            </span>
            <h3 class="mt-6 font-display text-2xl font-bold text-stone-900">Merci, c'est envoyé !</h3>
            <p class="mt-3 max-w-sm text-stone-600">
                Votre demande est bien arrivée. Nous vous répondons sous 24 h avec un devis clair, sans engagement.
            </p>
            <button
                type="button"
                wire:click="sendAnother"
                class="mt-8 text-sm font-semibold text-ember-600 underline-offset-4 hover:underline focus:outline-hidden focus-visible:underline"
            >
                Envoyer une autre demande
            </button>
        </div>
    @else
        <h3 class="text-xl font-semibold text-stone-900">Demande de devis</h3>
        <p class="mt-1 text-sm text-stone-500">Tous les champs sont obligatoires, sauf le téléphone.</p>

        <form wire:submit="submit" novalidate class="mt-6 space-y-5">
            <div>
                <label for="contact-name" class="block text-sm font-medium text-stone-700">Nom complet</label>
                <input
                    id="contact-name"
                    type="text"
                    wire:model.live.blur="name"
                    autocomplete="name"
                    enterkeyhint="next"
                    required
                    @error('name') aria-invalid="true" aria-describedby="contact-name-error" @enderror
                    class="{{ $inputClass('name') }}"
                >
                @error('name')
                    <p id="contact-name-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="contact-email" class="block text-sm font-medium text-stone-700">Adresse e-mail</label>
                    <input
                        id="contact-email"
                        type="email"
                        wire:model.live.blur="email"
                        autocomplete="email"
                        inputmode="email"
                        enterkeyhint="next"
                        required
                        @error('email') aria-invalid="true" aria-describedby="contact-email-error" @enderror
                        class="{{ $inputClass('email') }}"
                    >
                    @error('email')
                        <p id="contact-email-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact-phone" class="block text-sm font-medium text-stone-700">
                        Téléphone <span class="font-normal text-stone-400">(optionnel)</span>
                    </label>
                    <input
                        id="contact-phone"
                        type="tel"
                        wire:model.live.blur="phone"
                        autocomplete="tel"
                        inputmode="tel"
                        enterkeyhint="next"
                        @error('phone') aria-invalid="true" aria-describedby="contact-phone-error" @enderror
                        class="{{ $inputClass('phone') }}"
                    >
                    @error('phone')
                        <p id="contact-phone-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <fieldset @error('preferredTime') aria-describedby="contact-time-error" @enderror>
                <legend class="block text-sm font-medium text-stone-700">Créneau souhaité</legend>
                <p class="mt-0.5 text-sm text-stone-500">Le moment de la journée qui vous arrange le mieux.</p>

                <div class="mt-2.5 grid grid-cols-3 gap-2 sm:gap-3">
                    @foreach (config('site.time_slots') as $value => $slot)
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="contact-preferred-time"
                                value="{{ $value }}"
                                wire:model.live="preferredTime"
                                class="peer sr-only"
                            >
                            <span
                                @class([
                                    'flex h-full flex-col items-center gap-1 rounded-xl border bg-white px-1.5 py-3 text-center text-stone-700 transition hover:border-stone-400',
                                    'peer-checked:border-ember-500 peer-checked:bg-ember-50 peer-checked:text-ember-700 peer-checked:ring-1 peer-checked:ring-ember-500',
                                    'peer-focus-visible:ring-2 peer-focus-visible:ring-ember-500',
                                    'border-red-400' => $errors->has('preferredTime'),
                                    'border-stone-300' => ! $errors->has('preferredTime'),
                                ])
                            >
                                <x-icon :name="$slot['icon']" class="h-6 w-6" stroke-width="1.6" />
                                <span class="text-sm font-semibold">{{ $slot['label'] }}</span>
                                <span class="text-xs opacity-70 tabular-nums">{{ $slot['hours'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('preferredTime')
                    <p id="contact-time-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>

            <div>
                <label for="contact-message" class="block text-sm font-medium text-stone-700">Votre véhicule et votre besoin</label>
                <textarea
                    id="contact-message"
                    wire:model.live.blur="message"
                    rows="5"
                    required
                    placeholder="Ex. : Peugeot 3008, nettoyage intérieur complet et lustrage."
                    @error('message') aria-invalid="true" aria-describedby="contact-message-error" @enderror
                    class="{{ $inputClass('message') }}"
                ></textarea>
                @error('message')
                    <p id="contact-message-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Honeypot: invisible to visitors, so only bots fill it in. --}}
            <div class="hidden" aria-hidden="true">
                <label for="contact-website">Ne pas remplir</label>
                <input id="contact-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            @error('form')
                <p class="rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-red-100" role="alert">{{ $message }}</p>
            @enderror

            <x-brand-button type="submit" size="lg" class="w-full sm:w-auto" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">Envoyer ma demande</span>
                <span wire:loading wire:target="submit">Envoi en cours…</span>
            </x-brand-button>

            <p class="text-xs leading-relaxed text-stone-500">
                Vos coordonnées servent uniquement à répondre à votre demande. Elles ne sont jamais transmises à des tiers.
            </p>
        </form>
    @endif
</div>
