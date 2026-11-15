<?php

use App\Models\Message;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public string $website = '';

    public int $startedAt = 0;

    public bool $sent = false;

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
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:180'],
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'message.min' => 'Please share at least a couple of sentences so I can respond meaningfully.',
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
            'subject' => 'subject',
            'message' => 'message',
        ];
    }

    public function submit(): void
    {
        if (filled($this->website)) {
            $this->sent = true;

            return;
        }

        if ($this->startedAt > 0 && now()->timestamp - $this->startedAt < 3) {
            $this->addError('form', 'Please take a moment to review your message before sending.');

            return;
        }

        $rateLimitKey = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->addError('form', 'Too many messages have been sent from this connection. Please try again later.');

            return;
        }

        $data = $this->validate();

        Message::query()->create([
            'name' => trim(strip_tags($data['name'])),
            'email' => mb_strtolower(trim($data['email'])),
            'subject' => trim(strip_tags($data['subject'])),
            'message' => trim(strip_tags($data['message'])),
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 1000),
        ]);

        RateLimiter::hit($rateLimitKey, 600);

        $this->reset(['name', 'email', 'subject', 'message', 'website']);
        $this->resetValidation();
        $this->startedAt = now()->timestamp;
        $this->sent = true;
    }

    public function writeAnother(): void
    {
        $this->sent = false;
        $this->startedAt = now()->timestamp;
        $this->resetValidation();
    }
};
?>

<div>
    @if ($sent)
        <div class="border border-line bg-surface p-8 sm:p-10" role="status">
            <span class="grid h-14 w-14 place-items-center border border-accent text-accent">
                <x-ui-icon name="check" class="h-6 w-6" />
            </span>

            <h2 class="mt-6 font-display text-3xl font-medium tracking-[-0.03em]">Message received.</h2>
            <p class="prose-editorial mt-3 max-w-md">
                Thank you for reaching out. Your message is stored securely and will appear in the admin inbox. Expect a reply if you included a working email address.
            </p>

            <button type="button" wire:click="writeAnother" class="btn-secondary mt-8">Write another message</button>
        </div>
    @else
        <form wire:submit="submit" class="space-y-6" novalidate>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="contact-name" class="field-label">Name <span class="text-accent">*</span></label>
                    <input
                        id="contact-name"
                        type="text"
                        wire:model="name"
                        class="field @error('name') border-accent @enderror"
                        autocomplete="name"
                        placeholder="Your name"
                        @error('name') aria-invalid="true" aria-describedby="contact-name-error" @enderror
                    >
                    @error('name')
                        <p id="contact-name-error" class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact-email" class="field-label">Email <span class="text-accent">*</span></label>
                    <input
                        id="contact-email"
                        type="email"
                        wire:model="email"
                        class="field @error('email') border-accent @enderror"
                        autocomplete="email"
                        placeholder="you@example.com"
                        @error('email') aria-invalid="true" aria-describedby="contact-email-error" @enderror
                    >
                    @error('email')
                        <p id="contact-email-error" class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="contact-subject" class="field-label">Subject <span class="text-accent">*</span></label>
                <input
                    id="contact-subject"
                    type="text"
                    wire:model="subject"
                    class="field @error('subject') border-accent @enderror"
                    placeholder="What is this about?"
                    @error('subject') aria-invalid="true" aria-describedby="contact-subject-error" @enderror
                >
                @error('subject')
                    <p id="contact-subject-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-baseline justify-between gap-4">
                    <label for="contact-message" class="field-label">Message <span class="text-accent">*</span></label>
                    <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">Max 5,000 characters</span>
                </div>
                <textarea
                    id="contact-message"
                    wire:model="message"
                    rows="7"
                    class="field resize-y @error('message') border-accent @enderror"
                    placeholder="Tell me about the project, role, or idea you have in mind."
                    @error('message') aria-invalid="true" aria-describedby="contact-message-error" @enderror
                ></textarea>
                @error('message')
                    <p id="contact-message-error" class="field-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Honeypot: hidden from humans and assistive technology. --}}
            <div class="hidden" aria-hidden="true">
                <label for="contact-website">Website</label>
                <input id="contact-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            @error('form')
                <p class="border-s-2 border-accent bg-accent-soft px-4 py-3 text-sm text-ink" role="alert">{{ $message }}</p>
            @enderror

            <div class="flex flex-col gap-4 border-t border-line pt-6 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-sm text-xs leading-relaxed text-faint">
                    Your details are used only to respond to this message. No marketing, no sharing.
                </p>

                <button type="submit" class="btn-primary shrink-0" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Send message</span>
                    <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                        <x-ui-icon name="loader" class="h-4 w-4 animate-spin" />
                        Sending
                    </span>
                    <x-ui-icon name="send" class="h-4 w-4" />
                </button>
            </div>
        </form>
    @endif
</div>
