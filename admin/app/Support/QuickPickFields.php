<?php

namespace App\Support;

/**
 * Quick Edit modal field naming — tips use {@code predictions.type}; odds use a fixed suffix.
 */
final class QuickPickFields
{
    public const ODDS_SUFFIX = 'niceodds';

    public static function oddsPayloadKey(string $typeKey): string
    {
        return $typeKey.'_'.self::ODDS_SUFFIX;
    }

    public static function isOddsPayloadKey(string $key): bool
    {
        return $key !== '' && str_ends_with($key, '_'.self::ODDS_SUFFIX);
    }

    public static function typeKeyFromOddsPayloadKey(string $key): string
    {
        $suffix = '_'.self::ODDS_SUFFIX;

        return str_ends_with($key, $suffix) ? substr($key, 0, -strlen($suffix)) : $key;
    }
}
