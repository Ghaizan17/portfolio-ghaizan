@extends('layouts.app')

@section('title', 'Page not found')
@section('robots', 'noindex,nofollow')

@section('content')
    <section class="relative overflow-hidden">
        <div class="hero-grid pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-editorial relative flex min-h-[70svh] flex-col justify-center py-20">
            <p class="eyebrow mb-5 flex items-center gap-3">
                <span class="inline-block h-px w-8 bg-accent"></span>
                Error 404
            </p>

            <h1 class="display-1">This page drifted<br>out of orbit.</h1>

            <p class="body-lg mt-6 max-w-xl text-secondary">
                The address may be mistyped, the page may have moved, or the content was never published. The archive is still intact.
            </p>

            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" wire:navigate class="btn-primary">Back to home</a>
                <a href="{{ route('projects.index') }}" wire:navigate class="btn-secondary">Browse projects</a>
            </div>
        </div>
    </section>
@endsection
