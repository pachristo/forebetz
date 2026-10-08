<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'Dailysuretips'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | Blog (Filament) — same database as new_admin default (`blogs`, `blog_categories`)
        |--------------------------------------------------------------------------
        |
        | Override with DB_BLOG_* if blog tables ever live on another host/database.
        | Filament FileUpload uses the `public` disk → storage/app/public (e.g. blog-images/).
        |
        */
        'blog' => [
            'driver' => 'mysql',
            'url' => env('BLOG_DB_URL'),
            'host' => env('DB_BLOG_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('DB_BLOG_PORT', env('DB_PORT', '3306')),
            'database' => env('DB_BLOG_DATABASE', env('DB_DATABASE', 'Dailysuretips')),
            'username' => env('DB_BLOG_USERNAME', env('DB_USERNAME', 'root')),
            'password' => env('DB_BLOG_PASSWORD', env('DB_PASSWORD', '')),
            'unix_socket' => env('DB_BLOG_SOCKET', env('DB_SOCKET', '')),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | Legacy hero_newblog — WordPress / old nice_blog DB (same as new_front DB_*_2)
        |--------------------------------------------------------------------------
        |
        | Used by one-off migrations that copy from hero_newblog into the main app DB.
        |
        */
        'hero_blog' => [
            'driver' => 'mysql',
            'url' => env('HERO_BLOG_DB_URL'),
            'host' => env('DB_HOST_2', env('DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT_2', env('DB_PORT', '3306')),
            'database' => env('DB_DATABASE_2', env('DB_DATABASE', 'Dailysuretips')),
            'username' => env('DB_USERNAME_2', env('DB_USERNAME', 'root')),
            'password' => env('DB_PASSWORD_2', env('DB_PASSWORD', '')),
            'unix_socket' => env('DB_SOCKET_2', env('DB_SOCKET', '')),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | Legacy import (e.g. php artisan memberships:import-from-rara-old)
        |--------------------------------------------------------------------------
        */
        'rara_old' => [
            'driver' => 'mysql',
            'url' => env('RARA_OLD_DB_URL'),
            'host' => env('RARA_OLD_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('RARA_OLD_DB_PORT', env('DB_PORT', '3306')),
            'database' => env('RARA_OLD_DB_DATABASE', env('DB_DATABASE', 'Dailysuretips')),
            'username' => env('RARA_OLD_DB_USERNAME', env('DB_USERNAME', 'root')),
            'password' => env('RARA_OLD_DB_PASSWORD', env('DB_PASSWORD', '')),
            'unix_socket' => env('RARA_OLD_DB_SOCKET', env('DB_SOCKET', '')),
            'charset' => env('RARA_OLD_DB_CHARSET', env('DB_CHARSET', 'utf8mb4')),
            'collation' => env('RARA_OLD_DB_COLLATION', env('DB_COLLATION', 'utf8mb4_unicode_ci')),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | Legacy winsureodds_old — php artisan seo-pages:import-from-nice-vip --source=winsureodds_old
        |--------------------------------------------------------------------------
        */
        'winsureodds_old' => [
            'driver' => 'mysql',
            'url' => env('WINSUREODDS_OLD_DB_URL'),
            'host' => env('WINSUREODDS_OLD_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('WINSUREODDS_OLD_DB_PORT', env('DB_PORT', '3306')),
            'database' => env('WINSUREODDS_OLD_DB_DATABASE', 'winsureodds_old'),
            'username' => env('WINSUREODDS_OLD_DB_USERNAME', env('DB_USERNAME', 'root')),
            'password' => env('WINSUREODDS_OLD_DB_PASSWORD', env('DB_PASSWORD', '')),
            'unix_socket' => env('WINSUREODDS_OLD_DB_SOCKET', env('DB_SOCKET', '')),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | new_front main app DB (nice_vip) — php artisan memberships:import-from-nice-vip
        |--------------------------------------------------------------------------
        */
        'nice_vip' => [
            'driver' => 'mysql',
            'url' => env('NICE_VIP_DB_URL'),
            'host' => env('NICE_VIP_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'port' => env('NICE_VIP_DB_PORT', env('DB_PORT', '3306')),
            'database' => env('NICE_VIP_DB_DATABASE', env('DB_DATABASE', 'Dailysuretips')),
            'username' => env('NICE_VIP_DB_USERNAME', env('DB_USERNAME', 'root')),
            'password' => env('NICE_VIP_DB_PASSWORD', env('DB_PASSWORD', '')),
            'unix_socket' => env('NICE_VIP_DB_SOCKET', env('DB_SOCKET', '')),
            'charset' => env('NICE_VIP_DB_CHARSET', env('DB_CHARSET', 'utf8mb4')),
            'collation' => env('NICE_VIP_DB_COLLATION', env('DB_COLLATION', 'utf8mb4_unicode_ci')),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | Optional legacy DB named `new_vip` (same host as nice_vip by default)
        |--------------------------------------------------------------------------
        | php artisan ads:import-from-nice-vip --source=new_vip
        */
        'new_vip' => [
            'driver' => 'mysql',
            'url' => env('NEW_VIP_DB_URL'),
            'host' => env('NEW_VIP_DB_HOST', env('NICE_VIP_DB_HOST', env('DB_HOST', '127.0.0.1'))),
            'port' => env('NEW_VIP_DB_PORT', env('NICE_VIP_DB_PORT', env('DB_PORT', '3306'))),
            'database' => env('NEW_VIP_DB_DATABASE', env('DB_DATABASE', 'Dailysuretips')),
            'username' => env('NEW_VIP_DB_USERNAME', env('NICE_VIP_DB_USERNAME', env('DB_USERNAME', 'root'))),
            'password' => env('NEW_VIP_DB_PASSWORD', env('NICE_VIP_DB_PASSWORD', env('DB_PASSWORD', ''))),
            'unix_socket' => env('NEW_VIP_DB_SOCKET', env('NICE_VIP_DB_SOCKET', env('DB_SOCKET', ''))),
            'charset' => env('NEW_VIP_DB_CHARSET', env('NICE_VIP_DB_CHARSET', env('DB_CHARSET', 'utf8mb4'))),
            'collation' => env('NEW_VIP_DB_COLLATION', env('NICE_VIP_DB_COLLATION', env('DB_COLLATION', 'utf8mb4_unicode_ci'))),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'Dailysuretips'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'Dailysuretips'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'Dailysuretips'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
