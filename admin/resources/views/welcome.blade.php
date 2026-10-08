<!DOCTYPE html>
<html lang="en">
@php
    $siteConfig = \App\Models\SiteConfiguration::query()->latest('id')->first();

    $resolveMediaUrl = static function (?string $path): ?string {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    };

    $siteFaviconUrl = $resolveMediaUrl($siteConfig?->favicon) ?? asset('assets/favicon.png');
    $siteLogoUrl = $resolveMediaUrl($siteConfig?->logo) ?? asset('assets/logo-dark.png');
    $loginUrl = url('/admin/login');
    $redirectMs = 2800;
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Loading | Dailysuretips Admin</title>
    <link rel="icon" href="{{ $siteFaviconUrl }}">
    <noscript><meta http-equiv="refresh" content="0;url={{ $loginUrl }}"></noscript>
    <style>
        :root {
            --primary: #ff6900;
            --primary-deep: #cc5400;
            --bg: #0a0a0a;
            --redirect-ms: {{ $redirectMs }}ms;
        }

        * { box-sizing: border-box; }

        html, body { height: 100%; margin: 0; }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background:
                radial-gradient(circle at 50% 42%, rgba(255, 105, 0, 0.16), transparent 42%),
                var(--bg);
            color: #fff;
            font-family: 'Albert Sans', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .splash {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 28px;
            text-align: center;
        }

        .mark {
            position: relative;
            width: 112px;
            height: 112px;
            display: grid;
            place-items: center;
        }

        .mark img {
            position: relative;
            z-index: 1;
            width: 88px;
            height: 88px;
            object-fit: contain;
            animation: mark-in 0.7s cubic-bezier(.2, .9, .3, 1.3) both, mark-breathe 1.6s ease-in-out 0.7s infinite;
        }

        .mark::before,
        .mark::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid var(--primary);
            opacity: 0;
            animation: ring 1.8s ease-out 0.5s infinite;
        }

        .mark::after { animation-delay: 1.4s; }

        .wordmark {
            height: 30px;
            width: auto;
            opacity: 0;
            animation: rise 0.6s ease-out 0.35s forwards;
        }

        .status {
            margin: 0;
            font-size: 14px;
            letter-spacing: 0.04em;
            color: rgba(255, 255, 255, 0.72);
            opacity: 0;
            animation: rise 0.6s ease-out 0.5s forwards;
        }

        .bar {
            width: 220px;
            height: 4px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            overflow: hidden;
            opacity: 0;
            animation: rise 0.6s ease-out 0.5s forwards;
        }

        .bar span {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--primary-deep), var(--primary), #ff9a4d);
            box-shadow: 0 0 12px rgba(255, 105, 0, 0.6);
            animation: fill var(--redirect-ms) cubic-bezier(.4, 0, .2, 1) forwards;
        }

        .skip {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            opacity: 0;
            animation: rise 0.6s ease-out 1.2s forwards;
        }

        .skip:hover { color: var(--primary); }

        @keyframes mark-in {
            from { opacity: 0; transform: scale(0.4) rotate(-12deg); }
            to { opacity: 1; transform: scale(1) rotate(0); }
        }

        @keyframes mark-breathe {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(255, 105, 0, 0)); }
            50% { transform: scale(1.06); filter: drop-shadow(0 0 18px rgba(255, 105, 0, 0.55)); }
        }

        @keyframes ring {
            0% { opacity: 0.7; transform: scale(0.7); }
            100% { opacity: 0; transform: scale(1.6); }
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fill {
            to { width: 100%; }
        }

        body.leaving .splash {
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            transform: scale(0.96);
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; }
            .bar span { width: 100%; }
        }
    </style>
</head>

<body>
    <main class="splash" role="status" aria-live="polite">
        <div class="mark">
            <img src="{{ $siteFaviconUrl }}" alt="" width="88" height="88">
        </div>
        <img class="wordmark" src="{{ $siteLogoUrl }}" alt="Dailysuretips" width="169" height="30">
        <p class="status" id="status">Preparing your dashboard…</p>
        <div class="bar" aria-hidden="true"><span></span></div>
        <a class="skip" href="{{ $loginUrl }}">Continue to login</a>
    </main>

    <script>
        (function () {
            var loginUrl = @json($loginUrl);
            var total = {{ $redirectMs }};
            var status = document.getElementById('status');
            var started = Date.now();

            var tick = setInterval(function () {
                var left = Math.max(0, Math.ceil((total - (Date.now() - started)) / 1000));
                status.textContent = left > 0 ? 'Redirecting to login in ' + left + 's…' : 'Opening login…';
            }, 250);

            setTimeout(function () {
                clearInterval(tick);
                document.body.classList.add('leaving');
                setTimeout(function () { window.location.replace(loginUrl); }, 280);
            }, total);
        })();
    </script>
</body>
</html>
