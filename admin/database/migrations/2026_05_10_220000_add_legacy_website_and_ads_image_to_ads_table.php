<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy nice_vip / new_front blades read {@code website} and {@code ads_image};
 * Filament uses {@code link} and {@code image}. Import/sync commands populate both.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ads')) {
            return;
        }

        Schema::table('ads', function (Blueprint $table): void {
            if (! Schema::hasColumn('ads', 'website')) {
                $table->longText('website')->nullable()->after('link');
            }
            if (! Schema::hasColumn('ads', 'ads_image')) {
                $table->string('ads_image', 255)->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ads')) {
            return;
        }

        Schema::table('ads', function (Blueprint $table): void {
            if (Schema::hasColumn('ads', 'ads_image')) {
                $table->dropColumn('ads_image');
            }
            if (Schema::hasColumn('ads', 'website')) {
                $table->dropColumn('website');
            }
        });
    }
};
