<?php

use Database\Seeders\SiteTextFilesSeeder;
use Illuminate\Database\Migrations\Migration;

/**
 * Re-run SEO text seed after deploy if rows are missing/empty.
 * Safe: never overwrites non-empty content.
 */
return new class extends Migration
{
    public function up(): void
    {
        (new SiteTextFilesSeeder)->run();
    }

    public function down(): void
    {
        // Keep seeded content; table drop lives on the create migration.
    }
};
