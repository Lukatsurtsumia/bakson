<?php

namespace Tests\Feature;

use App\Livewire\ContactForm;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<string, string>
     */
    private array $validInput = [
        'name' => 'Camille Dupont',
        'email' => 'camille.dupont@example.com',
        'phone' => '06 12 34 56 78',
        'preferredTime' => 'evening',
        'message' => 'Bonjour, je souhaite un devis pour un detailing complet de ma Peugeot 3008.',
    ];

    /**
     * Fill the form in and wait like a person would, past the minimum fill time.
     *
     * @param  array<string, string>  $overrides
     */
    private function filledForm(array $overrides = []): Testable
    {
        $form = Livewire::test(ContactForm::class)->set([...$this->validInput, ...$overrides]);
        $this->travel(ContactForm::MIN_FILL_SECONDS + 2)->seconds();

        return $form;
    }

    public function test_a_valid_request_is_saved_and_emailed_to_the_business(): void
    {
        Mail::fake();

        $this->filledForm()
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true)
            ->assertSet('name', '')
            ->assertSee('Merci, c\'est envoyé !', false);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Camille Dupont',
            'email' => 'camille.dupont@example.com',
            'phone' => '06 12 34 56 78',
            'preferred_time' => 'evening',
        ]);

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo('devis@bakson.test')
                && $mail->hasReplyTo('camille.dupont@example.com', 'Camille Dupont')
                && $mail->hasSubject('Nouvelle demande de devis : Camille Dupont');
        });
    }

    public function test_the_phone_number_is_optional(): void
    {
        Mail::fake();

        $this->filledForm(['phone' => ''])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseHas('contact_messages', ['email' => 'camille.dupont@example.com', 'phone' => null]);
    }

    public function test_name_email_time_slot_and_message_are_required(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->call('submit')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'preferredTime' => 'required', 'message' => 'required'])
            ->assertSee('Merci d\'indiquer votre nom.')
            ->assertSet('sent', false);

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_only_the_configured_time_slots_are_accepted(): void
    {
        Livewire::test(ContactForm::class)
            ->set('preferredTime', 'midnight')
            ->assertHasErrors(['preferredTime' => 'in'])
            ->assertSee('Merci de choisir un créneau.')
            ->set('preferredTime', 'morning')
            ->assertHasNoErrors('preferredTime');
    }

    public function test_a_field_is_checked_as_soon_as_it_is_filled_in(): void
    {
        Livewire::test(ContactForm::class)
            ->set('email', 'not-an-email')
            ->assertHasErrors(['email' => 'email'])
            ->assertSee('Merci d\'indiquer une adresse e-mail valide.')
            ->set('email', 'camille.dupont@example.com')
            ->assertHasNoErrors('email');
    }

    public function test_links_are_refused_in_the_message_and_the_name(): void
    {
        Mail::fake();

        $this->filledForm([
            'name' => 'www.cheap-seo.example',
            'message' => 'Best prices here: https://spam.example/offer',
        ])
            ->call('submit')
            ->assertHasErrors(['name', 'message'])
            ->assertSee('Merci de ne pas inclure de liens.');

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_bots_filling_the_honeypot_are_ignored(): void
    {
        Mail::fake();

        $this->filledForm(['website' => 'spam'])
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_bots_that_submit_instantly_are_ignored(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set($this->validInput)
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_the_fill_time_cannot_be_changed_from_the_browser(): void
    {
        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(ContactForm::class)->set('shownAt', 0);
    }

    public function test_the_same_request_sent_twice_is_stored_once(): void
    {
        Mail::fake();

        $this->filledForm()->call('submit')->assertSet('sent', true);
        $this->filledForm()->call('submit')->assertHasNoErrors()->assertSet('sent', true);

        $this->assertDatabaseCount('contact_messages', 1);
        Mail::assertSentCount(1);
    }

    public function test_one_visitor_is_limited_to_five_requests(): void
    {
        Mail::fake();

        foreach (range(1, ContactForm::MAX_PER_IP) as $attempt) {
            $this->filledForm(['message' => "Demande de devis numéro {$attempt}."])
                ->call('submit')
                ->assertHasNoErrors();
        }

        $this->filledForm(['message' => 'Une demande de trop.'])
            ->call('submit')
            ->assertHasErrors('form')
            ->assertSet('sent', false);

        $this->assertDatabaseCount('contact_messages', ContactForm::MAX_PER_IP);
        Mail::assertSentCount(ContactForm::MAX_PER_IP);
    }

    public function test_requests_from_everyone_are_capped_per_hour(): void
    {
        Mail::fake();

        foreach (range(1, ContactForm::MAX_PER_HOUR) as $attempt) {
            RateLimiter::hit('contact-form:all', 3600);
        }

        $this->filledForm()
            ->call('submit')
            ->assertHasErrors('form');

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_turnstile_lets_verified_visitors_through(): void
    {
        Mail::fake();
        config(['services.turnstile.site_key' => 'site-key', 'services.turnstile.secret_key' => 'secret-key']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        $this->filledForm(['captchaToken' => 'valid-token'])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseCount('contact_messages', 1);
        Http::assertSent(fn ($request) => $request['secret'] === 'secret-key' && $request['response'] === 'valid-token');
    }

    public function test_turnstile_blocks_unverified_visitors(): void
    {
        Mail::fake();
        config(['services.turnstile.site_key' => 'site-key', 'services.turnstile.secret_key' => 'secret-key']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

        $this->filledForm(['captchaToken' => 'forged-token'])
            ->call('submit')
            ->assertHasErrors('captchaToken')
            ->assertSet('captchaToken', '')
            ->assertDispatched('turnstile-reset')
            ->assertSet('sent', false);

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_the_turnstile_widget_only_shows_when_configured(): void
    {
        Livewire::test(ContactForm::class)->assertDontSee('challenges.cloudflare.com');

        config(['services.turnstile.site_key' => 'site-key', 'services.turnstile.secret_key' => 'secret-key']);

        Livewire::test(ContactForm::class)->assertSee('site-key');
    }

    public function test_a_mail_failure_still_keeps_the_request_and_thanks_the_visitor(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP is down'));

        $this->filledForm()
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseHas('contact_messages', ['email' => 'camille.dupont@example.com']);
    }

    public function test_the_email_shows_the_visitors_request(): void
    {
        $contactMessage = ContactMessage::factory()->create([
            'name' => 'Camille Dupont',
            'phone' => null,
            'preferred_time' => 'evening',
            'message' => "Bonjour,\nun devis pour un lustrage svp.",
        ]);

        $mail = new ContactMessageReceived($contactMessage);

        $mail->assertSeeInHtml('Camille Dupont');
        $mail->assertSeeInHtml('non renseigné');
        $mail->assertSeeInHtml('Soir (17h – 19h)');
        $mail->assertSeeInHtml('un devis pour un lustrage svp.');
    }
}
