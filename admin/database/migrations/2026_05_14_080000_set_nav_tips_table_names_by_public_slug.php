<?php

use App\Support\FreeTipsNavTipsTableNamesBySlug;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->gameCatDatabaseConnections() as $conn) {
            try {
                FreeTipsNavTipsTableNamesBySlug::applyToConnection($conn);
            } catch (\Throwable) {
                continue;
            }
        }
    }

    /**
     * @return list<string>
     */
    private function gameCatDatabaseConnections(): array
    {
        $names = [(string) config('database.default')];
        if (is_array(config('database.connections.new_nice'))) {
            $names[] = 'new_nice';
        }

        return array_values(array_unique(array_filter($names)));
    }

    public function down(): void
    {
        // Non-reversible.
    }
};
