<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Forebetz') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? '') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        page: '#0a111d',
                        accent: '#fcbd02',
                        navy: '#162640',
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
            --page-bg: #0a111d;
            --accent: #fcbd02;
            --header-glass: rgba(137, 137, 137, 0.15);
            --navy: #162640;
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
