@extends('layouts.app')

@section('title', 'Guestbook')

@section('description', $siteSettings['guestbook_intro'] ?? 'Leave a message, a note, or a piece of advice.')

@section('content')
    <x-page-hero
        eyebrow="Guestbook"
        title="Leave a trace."
        :description="$siteSettings['guestbook_intro'] ?? 'Leave a message, a note, or a piece of advice. Entries appear after moderation.'"
    >
        <x-slot:aside>
            <div class="border border-line p-5">
                <p class="eyebrow">Moderated</p>
                <p class="mt-2 text-xs leading-relaxed text-secondary">
                    Every entry is reviewed before publication. Email addresses are never shown.
                </p>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <section class="section border-b border-line">
        <div class="container-editorial grid gap-14 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20">
            <div data-reveal>
                <p class="eyebrow mb-4">Sign in</p>
                <h2 class="display-3">A few words.</h2>
                <p class="prose-editorial mt-4 max-w-sm">
                    A note about the work, a correction, a question, or simply a hello. Keep it human.
                </p>

                <div class="mt-8 border-t border-line pt-6">
                    <p class="eyebrow mb-2">Published entries</p>
                    <p class="font-display text-4xl font-medium tracking-[-0.04em]">{{ str_pad((string) $totalApproved, 2, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            <div class="border border-line bg-surface p-6 sm:p-9" data-reveal>
                <livewire:guestbook-form />
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-editorial">
            <div class="mb-10 flex items-end justify-between gap-4 border-b border-line pb-4">
                <h2 class="display-3">Recent notes</h2>
                <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">Approved only</span>
            </div>

            @if ($entries->isEmpty())
                <x-empty-state
                    icon="message-circle"
                    title="The guestbook is still blank"
                    description="Be the first to leave a note. Approved entries will appear here."
                />
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($entries as $entry)
                        <article class="flex flex-col border border-line bg-surface p-6" data-reveal>
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center border border-line bg-elevated font-display text-xs font-medium text-faint">
                                    {{ \Illuminate\Support\Str::of($entry->name)->explode(' ')->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium">{{ $entry->name }}</p>
                                    <time datetime="{{ $entry->created_at->toDateString() }}" class="text-[0.625rem] uppercase tracking-[0.12em] text-faint">{{ $entry->created_at->format('M j, Y') }}</time>
                                </div>
                            </div>

                            <p class="mt-5 flex-1 font-display text-lg leading-relaxed tracking-[-0.01em]">{{ $entry->message }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
