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
            if (! Schema::hasColumn('partners', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (! Schema::hasColumn('partners', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('partners')) {
            return;
        }

        Schema::table('partners', function (Blueprint $table) {
            if (Schema::hasColumn('partners', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('partners', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
