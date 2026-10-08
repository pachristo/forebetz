<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_configurations', function (Blueprint $table) {
            if (! Schema::hasColumn('site_configurations', 'tiktok_link')) {
                $table->string('tiktok_link', 512)->nullable();
            }
            if (! Schema::hasColumn('site_configurations', 'instagram_link')) {
                $table->string('instagram_link', 512)->nullable();
            }
            if (! Schema::hasColumn('site_configurations', 'linkedin_link')) {
                $table->string('linkedin_link', 512)->nullable();
            }
            if (! Schema::hasColumn('site_configurations', 'contact_phone')) {
                $table->string('contact_phone', 64)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_configurations', function (Blueprint $table) {
            foreach (['tiktok_link', 'instagram_link', 'linkedin_link', 'contact_phone'] as $col) {
                if (Schema::hasColumn('site_configurations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
