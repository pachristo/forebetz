<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('site.title') }}</title>
    <meta name="description" content="{{ $description ?? config('site.description') }}">
    @if (! empty($keywords))
        <meta name="keywords" content="{{ $keywords }}">
    @endif
    @php($seoTitle = $title ?? config('site.title'))
    @php($seoDescription = $description ?? config('site.description'))
    @php($seoUrl = $canonical ?? url()->current())
    @php($seoImage = $image ?? url($asset.'/images/brand/logo.png'))
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ config('site.name', config('app.name')) }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <link rel="icon" href="{{ $asset }}/images/brand/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $asset }}/images/brand/favicon-32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $asset }}/images/brand/favicon-192.png">
    <link rel="apple-touch-icon" href="{{ $asset }}/images/brand/apple-touch-icon.png">
    <meta name="theme-color" content="#000000">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="min-h-screen font-sans text-white antialiased">
    <x-page-background />

    @php($showAds = ! ($hideAds ?? $hideHeader ?? false))
    @if ($showAds)
        <x-layout::ads.catfish position="header" />
    @endif

    <div class="relative min-h-screen overflow-x-clip">
        <div class="relative z-10 flex flex-col">
            @unless ($hideHeader ?? false)
                <livewire:layout::header />
            @endunless

            {{ $slot }}
        </div>
    </div>

    @unless ($hideSiteFooter ?? false)
        <livewire:layout::footer />
    @endunless

    @unless ($hideMobileNav ?? false)
        <livewire:layout::mobile-nav />
        <livewire:layout::tips-drawer />
    @endunless
    <livewire:layout::mobile-menu />

    @if ($showAds)
        <x-layout::ads.catfish position="footer" />
        <x-layout::ads.popup />
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            // One overlay (mobile menu / tips drawer) at a time; locks page scroll while open.
            Alpine.store('overlay', {
                active: null,
                is(name) { return this.active === name },
                open(name) { this.active = name; document.body.style.overflow = 'hidden' },
                close(name) { if (! name || this.active === name) { this.active = null; document.body.style.overflow = '' } },
                toggle(name) { this.is(name) ? this.close(name) : this.open(name) },
            });
        });
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
