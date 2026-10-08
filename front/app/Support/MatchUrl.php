<?php

namespace App\Support;

use Illuminate\Support\Str;

class MatchUrl
{
    public static function slug(?string $home, ?string $away): string
    {
        return Str::slug(trim((string) $home).' vs '.trim((string) $away)) ?: 'match';
    }

    public static function make(int|string $matchId, ?string $home, ?string $away): string
    {
        return '/'.$matchId.'/'.self::slug($home, $away);
    }
}
