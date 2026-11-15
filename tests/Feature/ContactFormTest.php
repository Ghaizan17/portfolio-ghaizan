<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
    }

    public function test_it_validates_required_fields(): void
    {
        $component = Livewire::test('contact-form');

        $this->travel(5)->seconds();

        $component->call('submit')
            ->assertHasErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_it_stores_a_valid_message(): void
    {
        $component = Livewire::test('contact-form')
            ->set('name', '  Ada Lovelace  ')
            ->set('email', 'ADA@EXAMPLE.COM')
            ->set('subject', '  Project enquiry  ')
            ->set('message', 'I would like to discuss a new Laravel application with you.');

        $this->travel(5)->seconds();

        $component->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $message = Message::query()->sole();

        $this->assertSame('Ada Lovelace', $message->name);
        $this->assertSame('ada@example.com', $message->email);
        $this->assertSame('Project enquiry', $message->subject);
        $this->assertFalse($message->is_read);
        $this->assertNotEmpty($message->ip_address);
    }

    public function test_the_honeypot_silently_discards_spam(): void
    {
        $component = Livewire::test('contact-form')
            ->set('name', 'Spam Bot')
            ->set('email', 'spam@example.com')
            ->set('subject', 'Cheap offers')
            ->set('message', 'Buy something from this obviously automated message.')
            ->set('website', 'https://spam.example.com');

        $this->travel(5)->seconds();

        $component->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_it_rejects_submissions_that_are_too_fast(): void
    {
        Livewire::test('contact-form')
            ->set('name', 'Fast Sender')
            ->set('email', 'fast@example.com')
            ->set('subject', 'Quick note')
            ->set('message', 'This message was submitted far too quickly to be human.')
            ->call('submit')
            ->assertHasErrors('form');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_it_rate_limits_repeated_submissions(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $component = Livewire::test('contact-form')
                ->set('name', 'Persistent Sender')
                ->set('email', 'persistent@example.com')
                ->set('subject', 'Message '.$attempt)
                ->set('message', 'This is a legitimate message that should pass every guard.');

            $this->travel(5)->seconds();

            $component->call('submit')->assertHasNoErrors();
        }

        $component = Livewire::test('contact-form')
            ->set('name', 'Persistent Sender')
            ->set('email', 'persistent@example.com')
            ->set('subject', 'Message four')
            ->set('message', 'This fourth message should be blocked by the rate limiter.');

        $this->travel(5)->seconds();

        $component->call('submit')->assertHasErrors('form');

        $this->assertDatabaseCount('messages', 3);
    }

    public function test_write_another_resets_the_confirmation_state(): void
    {
        Livewire::test('contact-form')
            ->set('sent', true)
            ->call('writeAnother')
            ->assertSet('sent', false)
            ->assertSet('startedAt', fn (int $startedAt): bool => $startedAt > 0);
    }
}
