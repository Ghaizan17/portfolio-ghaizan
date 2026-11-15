<?php

use App\Models\GuestbookEntry;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public string $website = '';

    public int $startedAt = 0;

    public bool $submitted = false;

    public function mount(): void
    {
        $this->startedAt = now()->timestamp;
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['nullable', 'string', 'email', 'max:180'],
            'message' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email address',
            'message' => 'message',
        ];
    }

    public function submit(): void
    {
        if (filled($this->website)) {
            $this->submitted = true;

            return;
        }

        if ($this->startedAt > 0 && now()->timestamp - $this->startedAt < 3) {
            $this->addError('form', 'Please take a moment before signing the guestbook.');

            return;
        }

        $rateLimitKey = 'guestbook-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->addError('form', 'Too many guestbook entries from this connection. Please try again later.');

            return;
        }

        $data = $this->validate();

        GuestbookEntry::query()->create([
            'name' => trim(strip_tags($data['name'])),
            'email' => filled($data['email']) ? mb_strtolower(trim($data['email'])) : null,
            'message' => trim(strip_tags($data['message'])),
            'status' => GuestbookEntry::STATUS_PENDING,
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 1000),
        ]);

        RateLimiter::hit($rateLimitKey, 600);

        $this->reset(['name', 'email', 'message', 'website']);
        $this->resetValidation();
        $this->startedAt = now()->timestamp;
        $this->submitted = true;
    }

    public function signAgain(): void
    {
        $this->submitted = false;
        $this->startedAt = now()->timestamp;
        $this->resetValidation();
    }
};
?>

<div>
    @if ($submitted)
        <div class="border border-line bg-surface p-8" role="status">
            <span class="grid h-12 w-12 place-items-center border border-accent text-accent">
                <x-ui-icon name="check" class="h-5 w-5" />
            </span>
            <h2 class="mt-5 font-display text-2xl font-medium tracking-[-0.025em]">Thanks for signing.</h2>
            <p class="mt-2 text-sm leading-relaxed text-secondary">
                Your entry is pending moderation. Once approved, it will appear in the guestbook below.
            </p>
            <button type="button" wire:click="signAgain" class="btn-secondary mt-6">Leave another note</button>
        </div>
    @else
        <form wire:submit="submit" class="space-y-5" novalidate>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="guestbook-name" class="field-label">Name <span class="text-accent">*</span></label>
                    <input
                        id="guestbook-name"
                        type="text"
                        wire:model="name"
                        class="field @error('name') border-accent @enderror"
                        autocomplete="name"
                        placeholder="Your name or handle"
                        @error('name') aria-invalid="true" aria-describedby="guestbook-name-error" @enderror
                    >
                    @error('name')
                        <p id="guestbook-name-error" class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="guestbook-email" class="field-label">Email <span class="text-faint">(private, optional)</span></label>
                    <input
                        id="guestbook-email"
                        type="email"
                        wire:model="email"
                        class="field @error('email') border-accent @enderror"
                        autocomplete="email"
                        placeholder="Never shown publicly"
                        @error('email') aria-invalid="true" aria-describedby="guestbook-email-error" @enderror
                    >
                    @error('email')
                        <p id="guestbook-email-error" class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <div class="flex items-baseline justify-between gap-4">
                    <label for="guestbook-message" class="field-label">Message <span class="text-accent">*</span></label>
                    <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">Max 1,000 characters</span>
                </div>
                <textarea
                    id="guestbook-message"
                    wire:model="message"
                    rows="4"
                    class="field resize-y @error('message') border-accent @enderror"
                    placeholder="A note, a thought, or a piece of advice."
                    @error('message') aria-invalid="true" aria-describedby="guestbook-message-error" @enderror
                ></textarea>
                @error('message')
                    <p id="guestbook-message-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="hidden" aria-hidden="true">
                <label for="guestbook-website">Website</label>
                <input id="guestbook-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            @error('form')
                <p class="border-s-2 border-accent bg-accent-soft px-4 py-3 text-sm text-ink" role="alert">{{ $message }}</p>
            @enderror

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-line pt-5">
                <p class="max-w-xs text-xs leading-relaxed text-faint">Entries are reviewed before appearing publicly.</p>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Sign the guestbook</span>
                    <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                        <x-ui-icon name="loader" class="h-4 w-4 animate-spin" />
                        Sending
                    </span>
                    <x-ui-icon name="arrow-right" class="h-4 w-4" />
                </button>
            </div>
        </form>
    @endif
</div>
