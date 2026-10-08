<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WordPress source (for imports)
    |--------------------------------------------------------------------------
    |
    | Base URL of the WordPress site (no trailing slash), e.g. the /news install.
    | REST API is assumed at {site_url}/wp-json/wp/v2
    |
    */
    'site_url' => rtrim((string) env('WORDPRESS_SITE_URL', 'https://dailysuretips.com/article'), '/'),

    /*
    | First path segment of the WordPress install (permalink base), e.g. "article" for
    | https://example.com/article/... or "news" for https://example.com/news/...
    */
    'path_prefix' => trim((string) env('WORDPRESS_PATH_PREFIX', 'article'), '/'),

];
