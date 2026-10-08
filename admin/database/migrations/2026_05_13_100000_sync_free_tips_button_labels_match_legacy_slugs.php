<?php

use App\Support\FreeTipsCategoryButtonLabels;
use Illuminate\Database\Migrations\Migration;

/**
 * Same widening as {@see new_front} migration — legacy slugs ({@code draws}, {@code btts_gg}, …) plus dashed paths.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->gameCatDatabaseConnections() as $conn) {
            try {
                FreeTipsCategoryButtonLabels::applyToConnection($conn);
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
