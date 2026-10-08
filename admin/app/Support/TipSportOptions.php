<?php

namespace App\Support;

/**
 * Shared sport keys for tip categories and manual fixtures (stored as lowercase slug).
 */
final class TipSportOptions
{
    public const DEFAULT = 'football';

    /** @return array<string, string> value => label */
    public static function selectOptions(): array
    {
        return [
            'football' => 'Football',
            'baseball' => 'Baseball',
            'basketball' => 'Basketball',
            'american_football' => 'American football',
            'tennis' => 'Tennis',
            'ice_hockey' => 'Ice hockey',
            'cricket' => 'Cricket',
            'rugby' => 'Rugby',
            'volleyball' => 'Volleyball',
            'handball' => 'Handball',
            'esports' => 'Esports',
            'other' => 'Other',
        ];
    }

    public static function label(string $key): string
    {
        return self::selectOptions()[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}
