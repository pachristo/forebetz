<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('partners')) {
            return;
        }

        Schema::table('partners', function (Blueprint $table) {
            if (! Schema::hasColumn('partners', 'logo')) {
                $table->string('logo')->nullable()->after('url');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('partners') || ! Schema::hasColumn('partners', 'logo')) {
            return;
        }

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
