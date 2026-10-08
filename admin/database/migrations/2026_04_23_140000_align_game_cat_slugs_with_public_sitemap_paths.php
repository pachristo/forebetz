<?php

use App\Support\TipCategoryPublicPathSlugs;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        TipCategoryPublicPathSlugs::syncSlugRows();
    }

    public function down(): void
    {
        // Intentionally empty: restoring previous slugs would need a snapshot.
    }
};
