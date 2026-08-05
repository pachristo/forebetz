<?php
/**
 * Vercel / front-controller — serves pages from /src.
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

$pagesRoot = realpath(__DIR__ . '/../src');
if ($pagesRoot === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Pages root missing';
    exit;
}

if ($path === '/' || $path === '') {
    $relative = 'index.php';
} else {
    $relative = ltrim($path, '/');
    if (!str_ends_with($relative, '.php')) {
        $withPhp = $relative . '.php';
        if (is_file($pagesRoot . '/' . $withPhp)) {
            $relative = $withPhp;
        }
    }
}

$target = realpath($pagesRoot . '/' . $relative);

if (
    $target === false
    || !str_starts_with($target, $pagesRoot)
    || !is_file($target)
    || !str_ends_with($target, '.php')
) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Not found';
    exit;
}

$_SERVER['SCRIPT_FILENAME'] = $target;
$_SERVER['SCRIPT_NAME'] = '/' . ltrim(str_replace('\\', '/', substr($target, strlen($pagesRoot))), '/');

chdir(dirname($target));
require $target;
