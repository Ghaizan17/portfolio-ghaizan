@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'robots' => 'index,follow',
    'canonical' => null,
])

@php
    $settings = $siteSettings ?? [];
    $siteTitle = $settings['site_title'] ?? config('app.name', 'Digital Dimension');
    $siteTagline = $settings['site_tagline'] ?? null;
    $defaultDescription = $settings['seo_default_description'] ?? $settings['site_description'] ?? $siteTagline;
    $resolvedTitle = filled($title) ? $title : ($settings['seo_default_title'] ?? $siteTitle);
    $fullTitle = filled($title) ? "{$title} — {$siteTitle}" : $resolvedTitle;
    $resolvedDescription = filled($description) ? $description : $defaultDescription;
    $resolvedCanonical = $canonical ?? url()->current();
    $resolvedImage = filled($image) ? $image : null;
    $keywords = $settings['seo_keywords'] ?? null;
@endphp

<title>{{ $fullTitle }}</title>

@if (filled($resolvedDescription))
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($resolvedDescription), 158) }}">
@endif

@if (filled($keywords))
    <meta name="keywords" content="{{ $keywords }}">
@endif

<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $resolvedCanonical }}">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<meta name="theme-color" content="#080808" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#f7f7f7" media="(prefers-color-scheme: light)">

<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteTitle }}">
<meta property="og:title" content="{{ $fullTitle }}">
@if (filled($resolvedDescription))
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($resolvedDescription), 158) }}">
@endif
<meta property="og:url" content="{{ $resolvedCanonical }}">
@if ($resolvedImage)
    <meta property="og:image" content="{{ $resolvedImage }}">
    <meta name="twitter:image" content="{{ $resolvedImage }}">
@endif

<meta name="twitter:card" content="{{ $resolvedImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $fullTitle }}">
@if (filled($resolvedDescription))
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($resolvedDescription), 158) }}">
@endif
