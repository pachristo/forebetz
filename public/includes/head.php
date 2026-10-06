<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dailysuretips') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? '') ?>">
    <link rel="icon" href="/assets/images/brand/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/brand/favicon-32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/images/brand/favicon-192.png">
    <link rel="apple-touch-icon" href="/assets/images/brand/apple-touch-icon.png">
    <meta name="theme-color" content="#000000">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        page: '#0a0a0a',
                        primary: '#ff6900',
                        accent: '#ff6900',
                        navy: '#1a1a1a',
                    },
                    fontFamily: {
                        sans: ['"Albert Sans"', 'sans-serif'],
                    },
                    maxWidth: {
                        site: '1728px',
                    },
                },
            },
        };
    </script>
    <style>
        :root {
            --page-bg: #0a0a0a;
            --primary: #ff6900;
            --accent: var(--primary);
            --header-glass: rgba(137, 137, 137, 0.15);
            --navy: #1a1a1a;
        }
        body {
            font-family: 'Albert Sans', sans-serif;
            background-color: var(--page-bg);
        }
        .header-glass {
            background: var(--header-glass);
            backdrop-filter: blur(7.45px);
            -webkit-backdrop-filter: blur(7.45px);
            border: none;
            box-shadow: none;
        }
        .countries-scroll::-webkit-scrollbar { width: 4px; }
        .countries-scroll::-webkit-scrollbar-thumb { background: #c4c4c4; border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen text-white antialiased">
<?php include __DIR__ . '/components/page-background.php'; ?>
