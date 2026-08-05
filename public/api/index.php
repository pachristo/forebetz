<?php
/**
 * Vercel PHP front controller (Root Directory = public).
 */
declare(strict_types=1);

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';
$path = rawurldecode($path);

if (str_contains($path, '..')) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Bad request';
    exit;
}

$publicRoot = realpath(__DIR__ . '/..');
if ($publicRoot === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Public root missing';
    exit;
}

if ($path === '/' || $path === '') {
    $relative = 'index.php';
} else {
    $relative = ltrim($path, '/');
    // Never route the api entry into itself / protect includes
    if (
        $relative === 'api/index.php'
        || str_starts_with($relative, 'api/')
        || str_starts_with($relative, 'includes/')
    ) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Not found';
        exit;
    }
    if (!str_ends_with($relative, '.php')) {
        $withPhp = $relative . '.php';
        if (is_file($publicRoot . '/' . $withPhp)) {
            $relative = $withPhp;
        }
    }
}

$target = realpath($publicRoot . '/' . $relative);

if (
    $target === false
    || !str_starts_with($target, $publicRoot)
    || !is_file($target)
    || !str_ends_with($target, '.php')
) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Not found';
    exit;
}

$_SERVER['SCRIPT_FILENAME'] = $target;
$_SERVER['SCRIPT_NAME'] = '/' . ltrim(str_replace('\\', '/', substr($target, strlen($publicRoot))), '/');

chdir(dirname($target));
require $target;
