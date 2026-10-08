<?php

namespace App\Support;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;

class CountryList
{
    /** @return array<string, string> country name => country name */
    public static function options(): array
    {
        return Cache::remember('site.country-options', now()->addDay(), fn () => Country::query()
            ->whereNotIn('country_name', ['World'])
            ->orderBy('country_name')
            ->pluck('country_name', 'country_name')
            ->all());
    }

    /** @return array<string, string> country name => lowercase flag code (e.g. "ng", "gb-eng") */
    public static function flagCodes(): array
    {
        return Cache::remember('site.country-flag-codes', now()->addDay(), fn () => Country::query()
            ->whereNotNull('country_id')
            ->pluck('country_id', 'country_name')
            ->map(fn (string $code) => strtolower($code))
            ->all());
    }
}
