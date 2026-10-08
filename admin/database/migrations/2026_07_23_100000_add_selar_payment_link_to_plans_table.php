<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-plan Selar checkout URL (external payment link).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table): void {
            if (! Schema::hasColumn('plans', 'selar_payment_link')) {
                $table->string('selar_payment_link', 500)->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table): void {
            if (Schema::hasColumn('plans', 'selar_payment_link')) {
                $table->dropColumn('selar_payment_link');
            }
        });
    }
};
