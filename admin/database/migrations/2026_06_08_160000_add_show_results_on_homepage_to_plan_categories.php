<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('plan_categories', 'show_results_on_homepage')) {
                $table->boolean('show_results_on_homepage')->default(false)->after('benefits');
            }
        });

        Schema::table('vip_recent_winnings', function (Blueprint $table) {
            $table->unique(['plan_category_id', 'winning_date'], 'vip_recent_winnings_plan_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('vip_recent_winnings', function (Blueprint $table) {
            $table->dropUnique('vip_recent_winnings_plan_date_unique');
        });

        Schema::table('plan_categories', function (Blueprint $table) {
            if (Schema::hasColumn('plan_categories', 'show_results_on_homepage')) {
                $table->dropColumn('show_results_on_homepage');
            }
        });
    }
};
