<?php

namespace App\Livewire;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Support\Turnstile;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

/**
 * The quote request form. Spam protection, cheapest first: a honeypot field, a minimum fill
 * time, no links, Cloudflare Turnstile (when configured), a duplicate guard, a per-IP limit,
 * and an hourly cap across all visitors that keeps Gmail's sending limit safe.
 */
class ContactForm extends Component
{
    /**
     * Humans need longer than this to fill the form in; bots submit almost instantly.
     */
    public const MIN_FILL_SECONDS = 3;

    public const MAX_PER_IP = 5;

    public const MAX_PER_HOUR = 30;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    /**
     * A key of config('site.time_slots'): morning, afternoon or evening.
     */
    public string $preferredTime = '';

    public string $message = '';

    /**
     * Honeypot: hidden from visitors, so only bots fill it in.
     */
    public string $website = '';

    /**
     * Token from the Turnstile widget, checked with Cloudflare on submit.
     */
    public string $captchaToken = '';

    /**
     * When the form was shown. Locked, so the browser can't change it.
     */
    #[Locked]
    public int $shownAt = 0;

    public bool $sent = false;

    public function mount(): void
    {
        $this->shownAt = now()->getTimestamp();
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', $this->withoutLinks(...)],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'preferredTime' => ['required', Rule::in(array_keys(config('site.time_slots')))],
            'message' => ['required', 'string', 'max:2000', $this->withoutLinks(...)],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Merci d\'indiquer votre nom.',
            'name.max' => 'Ce nom semble trop long.',
            'email.required' => 'Merci d\'indiquer votre adresse e-mail.',
            'email.email' => 'Merci d\'indiquer une adresse e-mail valide.',
            'email.max' => 'Cette adresse e-mail semble trop longue.',
            'phone.max' => 'Ce numéro de téléphone semble trop long.',
            'preferredTime.required' => 'Merci de choisir un créneau.',
            'preferredTime.in' => 'Merci de choisir un créneau.',
            'message.required' => 'Merci de décrire votre besoin.',
            'message.max' => 'Votre message est trop long (2000 caractères maximum).',
        ];
    }

    /**
     * Spam nearly always carries links; a real quote request doesn't need one.
     */
    private function withoutLinks(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && preg_match('~https?://|www\.|<a\s~i', $value)) {
            $fail('Merci de ne pas inclure de liens.');
        }
    }

    /**
     * Check each field as soon as the visitor leaves it or picks a time slot.
     */
    public function updated(string $property): void
    {
        if (array_key_exists($property, $this->rules())) {
            $this->validateOnly($property);
        }
    }

    public function submit(): void
    {
        $validated = $this->validate();

        // Bots: they fill the hidden field or submit faster than a person can type. Pretend it
        // worked so they don't adapt, but keep nothing.
        if ($this->website !== '' || now()->getTimestamp() - $this->shownAt < self::MIN_FILL_SECONDS) {
            $this->sent = true;

            return;
        }

        if (Turnstile::enabled() && ! Turnstile::verify($this->captchaToken, request()->ip())) {
            $this->addError('captchaToken', 'La vérification anti-robot a échoué. Merci de réessayer.');
            $this->resetCaptcha();

            return;
        }

        // The same request sent twice (double tap, reload): thank them, store it once.
        $duplicateKey = 'contact-form:duplicate:'.sha1(mb_strtolower($validated['email']).'|'.$validated['message']);

        if (RateLimiter::tooManyAttempts($duplicateKey, maxAttempts: 1)) {
            $this->finish();

            return;
        }

        $perIpKey = 'contact-form:ip:'.request()->ip();
        $globalKey = 'contact-form:all';

        if (RateLimiter::tooManyAttempts($perIpKey, self::MAX_PER_IP) || RateLimiter::tooManyAttempts($globalKey, self::MAX_PER_HOUR)) {
            $this->addError('form', 'Trop de demandes envoyées. Merci de réessayer un peu plus tard, ou appelez-nous directement.');
            $this->resetCaptcha();

            return;
        }

        RateLimiter::hit($duplicateKey, decaySeconds: 600);
        RateLimiter::hit($perIpKey, decaySeconds: 600);
        RateLimiter::hit($globalKey, decaySeconds: 3600);

        $contactMessage = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'preferred_time' => $validated['preferredTime'],
            'message' => $validated['message'],
        ]);

        try {
            Mail::to(config('site.contact_recipient'))->send(new ContactMessageReceived($contactMessage));
        } catch (Throwable $exception) {
            // The request is already saved, so a mail outage shouldn't fail the visitor's submission.
            report($exception);
        }

        $this->finish();
    }

    public function sendAnother(): void
    {
        $this->sent = false;
        $this->shownAt = now()->getTimestamp();
    }

    private function finish(): void
    {
        $this->reset('name', 'email', 'phone', 'preferredTime', 'message', 'captchaToken');
        $this->sent = true;

        // The thank-you card is shorter than the form, so bring it back into view.
        $this->js("document.getElementById('devis').scrollIntoView({ block: 'nearest' })");
    }

    /**
     * A Turnstile token works once; after a failed attempt the widget needs a fresh one.
     */
    private function resetCaptcha(): void
    {
        $this->captchaToken = '';
        $this->dispatch('turnstile-reset');
    }

    public function render(): View
    {
        return view('livewire.contact-form', [
            'turnstileSiteKey' => Turnstile::enabled() ? Turnstile::siteKey() : null,
        ]);
    }
}
