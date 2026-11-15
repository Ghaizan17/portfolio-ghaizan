<?php

namespace Tests\Feature;

use App\Models\GuestbookEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuestbookFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
    }

    public function test_it_requires_a_name_and_message(): void
    {
        $component = Livewire::test('guestbook-form');

        $this->travel(5)->seconds();

        $component->call('submit')
            ->assertHasErrors(['name', 'message']);
    }

    public function test_it_stores_a_pending_entry(): void
    {
        $component = Livewire::test('guestbook-form')
            ->set('name', '  Grace Hopper  ')
            ->set('email', 'GRACE@EXAMPLE.COM')
            ->set('message', 'Keep shipping small improvements every week.');

        $this->travel(5)->seconds();

        $component->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $entry = GuestbookEntry::query()->sole();

        $this->assertSame('Grace Hopper', $entry->name);
        $this->assertSame('grace@example.com', $entry->email);
        $this->assertSame(GuestbookEntry::STATUS_PENDING, $entry->status);
    }

    public function test_email_is_optional(): void
    {
        $component = Livewire::test('guestbook-form')
            ->set('name', 'Anonymous Reader')
            ->set('message', 'A small note without contact details.');

        $this->travel(5)->seconds();

        $component->call('submit')->assertHasNoErrors();

        $this->assertNull(GuestbookEntry::query()->sole()->email);
    }

    public function test_the_honeypot_does_not_store_an_entry(): void
    {
        $component = Livewire::test('guestbook-form')
            ->set('name', 'Spam Bot')
            ->set('message', 'Automated guestbook spam message.')
            ->set('website', 'https://spam.example.com');

        $this->travel(5)->seconds();

        $component->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseCount('guestbook_entries', 0);
    }

    public function test_pending_entries_do_not_appear_publicly_until_approved(): void
    {
        GuestbookEntry::factory()->create(['name' => 'Waiting Person']);
        GuestbookEntry::factory()->approved()->create(['name' => 'Published Person']);

        $this->get(route('guestbook'))
            ->assertOk()
            ->assertSee('Published Person')
            ->assertDontSee('Waiting Person');
    }
}
