<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_configurations', function (Blueprint $table): void {
            $table->unsignedBigInteger('homepage_results_plan_category_id')
                ->nullable()
                ->after('payment_proof_instruction');
            $table->date('homepage_results_week_date')
                ->nullable()
                ->after('homepage_results_plan_category_id');

            $table->foreign('homepage_results_plan_category_id', 'site_cfg_home_results_cat_fk')
                ->references('id')
                ->on('plan_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('site_configurations', function (Blueprint $table): void {
            $table->dropForeign('site_cfg_home_results_cat_fk');
            $table->dropColumn([
                'homepage_results_plan_category_id',
                'homepage_results_week_date',
            ]);
        });
    }
};
