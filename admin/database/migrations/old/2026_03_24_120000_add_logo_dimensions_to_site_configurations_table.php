<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_configurations', function (Blueprint $table) {
            $table->unsignedSmallInteger('logo_width')->nullable()->after('favicon');
            $table->unsignedSmallInteger('logo_height')->nullable()->after('logo_width');
        });
    }

    public function down(): void
    {
        Schema::table('site_configurations', function (Blueprint $table) {
            $table->dropColumn(['logo_width', 'logo_height']);
        });
    }
};
