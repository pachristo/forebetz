<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('member_subscriptions', 'subscription_id')) {
            DB::statement('ALTER TABLE member_subscriptions MODIFY subscription_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        DB::statement('UPDATE member_subscriptions SET subscription_id = 0 WHERE subscription_id IS NULL');
        DB::statement('ALTER TABLE member_subscriptions MODIFY subscription_id BIGINT UNSIGNED NOT NULL');
    }
};
