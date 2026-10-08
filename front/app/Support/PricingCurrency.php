<?php

namespace App\Support;

use App\Models\Plan;

/**
 * Maps a buyer's country to the plan price column and currency it is billed in.
 * Countries not listed pay in USD ("others" matches the admin payment-method country option).
 */
class PricingCurrency
{
    public const OTHERS = 'others';

    /** @var array<string, array{column: string, code: string, symbol: string, flag: string}> */
    private const COUNTRIES = [
        'Nigeria' => ['column' => 'price_ngn', 'code' => 'NGN', 'symbol' => '₦', 'flag' => 'ng'],
        'Ghana' => ['column' => 'price_ghs', 'code' => 'GHS', 'symbol' => 'GH₵', 'flag' => 'gh'],
        'Kenya' => ['column' => 'price_kes', 'code' => 'KES', 'symbol' => 'KSh ', 'flag' => 'ke'],
        'South Africa' => ['column' => 'price_zar', 'code' => 'ZAR', 'symbol' => 'R', 'flag' => 'za'],
        'Uganda' => ['column' => 'price_ugx', 'code' => 'UGX', 'symbol' => 'USh ', 'flag' => 'ug'],
        'Tanzania' => ['column' => 'price_tzs', 'code' => 'TZS', 'symbol' => 'TSh ', 'flag' => 'tz'],
        'Rwanda' => ['column' => 'price_rwf', 'code' => 'RWF', 'symbol' => 'FRw ', 'flag' => 'rw'],
        'Cameroon' => ['column' => 'price_xaf', 'code' => 'XAF', 'symbol' => 'FCFA ', 'flag' => 'cm'],
        'Zambia' => ['column' => 'price_zmw', 'code' => 'ZMW', 'symbol' => 'K', 'flag' => 'zm'],
        'Malawi' => ['column' => 'price_mwk', 'code' => 'MWK', 'symbol' => 'MK ', 'flag' => 'mw'],
    ];

    private const USD = ['column' => 'price_usd', 'code' => 'USD', 'symbol' => '$', 'flag' => 'us'];

    /**
     * Every country in the countries table (plus the local-currency ones), labelled with the currency it pays in.
     *
     * @return array<string, string> value => label
     */
    public static function options(): array
    {
        $names = array_unique([...array_keys(self::COUNTRIES), ...array_keys(CountryList::options())]);
        sort($names);

        $options = [];
        foreach ($names as $name) {
            $options[$name] = $name.' ('.self::for($name)['code'].')';
        }

        return $options + [self::OTHERS => 'Other countries (USD)'];
    }

    public static function normalise(?string $country): string
    {
        $country = (string) $country;

        return isset(self::COUNTRIES[$country]) || isset(CountryList::options()[$country]) ? $country : self::OTHERS;
    }

    /** @return array{column: string, code: string, symbol: string, flag: string} */
    public static function for(string $country): array
    {
        return self::COUNTRIES[$country] ?? self::USD;
    }

    public static function flagUrl(string $country): string
    {
        $code = self::COUNTRIES[$country]['flag'] ?? CountryList::flagCodes()[$country] ?? self::USD['flag'];

        return 'https://media.api-sports.io/flags/'.$code.'.svg';
    }

    /**
     * Price in the country's currency, falling back to USD when the plan has no local price.
     *
     * @return array{amount: float, code: string, label: string}|null
     */
    public static function price(Plan $plan, string $country): ?array
    {
        foreach ([self::for($country), self::USD] as $currency) {
            $amount = (float) $plan->{$currency['column']};

            if ($amount > 0) {
                return [
                    'amount' => $amount,
                    'code' => $currency['code'],
                    'label' => $currency['symbol'].number_format($amount, fmod($amount, 1.0) == 0.0 ? 0 : 2),
                ];
            }
        }

        return null;
    }
}
