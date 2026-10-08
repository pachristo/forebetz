<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vip_recent_winnings')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE vip_recent_winnings MODIFY status ENUM('won', 'lost', 'pending') NOT NULL DEFAULT 'won'");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE vip_recent_winnings DROP CONSTRAINT IF EXISTS vip_recent_winnings_status_check');
            DB::statement("ALTER TABLE vip_recent_winnings ADD CONSTRAINT vip_recent_winnings_status_check CHECK (status IN ('won', 'lost', 'pending'))");
        } else {
            // sqlite / others — recreate is heavy; leave column as free string if already flexible
            Schema::table('vip_recent_winnings', function (Blueprint $table) {
                // no-op for sqlite enum emulation
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('vip_recent_winnings')) {
            return;
        }

        DB::table('vip_recent_winnings')->where('status', 'pending')->update(['status' => 'won']);

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE vip_recent_winnings MODIFY status ENUM('won', 'lost') NOT NULL DEFAULT 'won'");
        }
    }
};
