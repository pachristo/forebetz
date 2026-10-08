<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (Schema::hasTable('ads')) {
                Schema::table('ads', function (Blueprint $table) {
                    if (! Schema::hasColumn('ads', 'expiry')) {
                        $table->dateTime('expiry')->nullable()->after('status');
                    }
                    if (! Schema::hasColumn('ads', 'contact')) {
                        $table->text('contact')->nullable()->after('expiry');
                    }
                    if (! Schema::hasColumn('ads', 'company')) {
                        $table->string('company')->nullable()->after('contact');
                    }
                    if (! Schema::hasColumn('ads', 'code')) {
                        $table->text('code')->nullable()->after('company');
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore; manual SQL may be required in some environments
        }
    }

    public function down(): void
    {
        try {
            if (Schema::hasTable('ads')) {
                Schema::table('ads', function (Blueprint $table) {
                    if (Schema::hasColumn('ads', 'code')) {
                        $table->dropColumn('code');
                    }
                    if (Schema::hasColumn('ads', 'company')) {
                        $table->dropColumn('company');
                    }
                    if (Schema::hasColumn('ads', 'contact')) {
                        $table->dropColumn('contact');
                    }
                    if (Schema::hasColumn('ads', 'expiry')) {
                        $table->dropColumn('expiry');
                    }
                });
            }
        } catch (\Throwable $e) {
        }
    }
};
