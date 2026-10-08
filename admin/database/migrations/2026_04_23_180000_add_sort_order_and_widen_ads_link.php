<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (! Schema::hasColumn('ads', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0);
            }
        });

        if (Schema::hasTable('ads')) {
            DB::statement('ALTER TABLE ads MODIFY COLUMN link TEXT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });

        if (Schema::hasTable('ads')) {
            DB::statement('ALTER TABLE ads MODIFY COLUMN link VARCHAR(255) NULL');
        }
    }
};
