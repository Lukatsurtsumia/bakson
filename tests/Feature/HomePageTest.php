<?php

namespace Tests\Feature;

use App\Livewire\ContactForm;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_renders_every_section(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('L\'éclat que votre voiture mérite.', false);
        $response->assertSee('Des soins sur-mesure pour votre véhicule');
        $response->assertSee('Avant / Après');
        $response->assertSee('Demandez votre devis gratuit');
        $response->assertSeeLivewire(ContactForm::class);
    }

    public function test_home_page_shows_the_business_details_from_config(): void
    {
        $response = $this->get('/');

        $response->assertSee(config('site.business.phone_display'));
        $response->assertSee(config('site.business.email'));

        foreach (config('site.projects') as $project) {
            $response->assertSee($project['title']);
        }

        foreach (config('site.time_slots') as $slot) {
            $response->assertSee($slot['label']);
        }
    }

    public function test_home_page_is_server_rendered_for_search_engines(): void
    {
        $response = $this->get('/');

        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('"@type":"AutomotiveBusiness"', false);
    }

    public function test_responses_carry_the_security_headers(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeaderMissing('X-Powered-By');
    }

    public function test_the_old_account_pages_no_longer_exist(): void
    {
        foreach (['/login', '/register', '/dashboard', '/profile', '/forgot-password'] as $uri) {
            $this->get($uri)->assertNotFound();
        }
    }
}
