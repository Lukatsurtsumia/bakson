<?php

namespace App\Livewire;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Throwable;

class ContactForm extends Component
{
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

    public bool $sent = false;

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'preferredTime' => ['required', Rule::in(array_keys(config('site.time_slots')))],
            'message' => ['required', 'string', 'max:2000'],
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

        if ($this->website !== '') {
            // A bot filled the honeypot: pretend it worked, but keep nothing.
            $this->sent = true;

            return;
        }

        $rateLimitKey = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, maxAttempts: 5)) {
            $this->addError('form', 'Trop de demandes envoyées. Merci de réessayer dans quelques minutes, ou appelez-nous directement.');

            return;
        }

        RateLimiter::hit($rateLimitKey, decaySeconds: 600);

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

        $this->reset('name', 'email', 'phone', 'preferredTime', 'message');
        $this->sent = true;

        // The thank-you card is shorter than the form, so bring it back into view.
        $this->js("document.getElementById('devis').scrollIntoView({ block: 'nearest' })");
    }

    public function sendAnother(): void
    {
        $this->sent = false;
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }
}
