<?php

namespace Tests\Feature;

use App\Livewire\ContactForm;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_a_valid_request_is_saved_and_emailed_to_the_business(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set($this->validInput)
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

        Livewire::test(ContactForm::class)
            ->set([...$this->validInput, 'phone' => ''])
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

    public function test_bots_filling_the_honeypot_are_ignored(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set([...$this->validInput, 'website' => 'https://spam.example'])
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_repeated_requests_are_rate_limited(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $attempt) {
            Livewire::test(ContactForm::class)
                ->set($this->validInput)
                ->call('submit')
                ->assertHasNoErrors();
        }

        Livewire::test(ContactForm::class)
            ->set($this->validInput)
            ->call('submit')
            ->assertHasErrors('form')
            ->assertSet('sent', false);

        $this->assertDatabaseCount('contact_messages', 5);
        Mail::assertSentCount(5);
    }

    public function test_a_mail_failure_still_keeps_the_request_and_thanks_the_visitor(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP is down'));

        Livewire::test(ContactForm::class)
            ->set($this->validInput)
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
