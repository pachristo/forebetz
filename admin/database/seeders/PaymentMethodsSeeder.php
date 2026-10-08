<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

/**
 * Starter payment methods: local options for African countries with their own price column,
 * and USD options for every other country ("others"). Account numbers and wallet addresses are
 * XXXX placeholders — replace them in Admin → Payment methods before going live.
 * Safe to re-run: rows are matched by country + name.
 */
class PaymentMethodsSeeder extends Seeder
{
    private const ACCOUNT_NAME = 'Dailysuretips';

    public function run(): void
    {
        foreach ($this->methods() as [$country, $name, $details]) {
            PaymentMethod::query()->updateOrCreate(
                ['country' => $country, 'name' => $name],
                ['instructions' => $this->html($details), 'text' => null],
            );
        }
    }

    /** @param  array<string, string>  $details */
    private function html(array $details): string
    {
        $rows = collect($details)
            ->map(fn (string $value, string $label) => '<p><strong>'.e($label).':</strong> '.e($value).'</p>')
            ->implode('');

        return $rows.'<p>Use your registered email as the payment reference/narration.</p>';
    }

    /** @return list<array{0: string, 1: string, 2: array<string, string>}> */
    private function methods(): array
    {
        $name = self::ACCOUNT_NAME;

        return [
            ['Nigeria', 'Bank Transfer', ['Bank' => 'XXXX Bank', 'Account name' => $name, 'Account number' => 'XXXXXXXXXX']],
            ['Nigeria', 'OPay / PalmPay', ['Wallet' => 'OPay', 'Account name' => $name, 'Account number' => 'XXXXXXXXXX']],
            ['Ghana', 'MTN Mobile Money', ['Network' => 'MTN MoMo', 'Name' => $name, 'MoMo number' => '+233 XX XXX XXXX']],
            ['Ghana', 'Telecel Cash', ['Network' => 'Telecel Cash', 'Name' => $name, 'Number' => '+233 XX XXX XXXX']],
            ['Kenya', 'M-Pesa', ['Paybill / Till' => 'XXXXXX', 'Account' => $name, 'Phone' => '+254 7XX XXX XXX']],
            ['Uganda', 'MTN / Airtel Money', ['MTN MoMo' => '+256 7XX XXX XXX', 'Airtel Money' => '+256 7XX XXX XXX', 'Name' => $name]],
            ['Tanzania', 'M-Pesa / Mixx by Yas', ['M-Pesa' => '+255 7XX XXX XXX', 'Mixx by Yas' => '+255 7XX XXX XXX', 'Name' => $name]],
            ['Rwanda', 'MTN Mobile Money', ['MoMo number' => '+250 7XX XXX XXX', 'Name' => $name]],
            ['Cameroon', 'MTN / Orange Money', ['MTN MoMo' => '+237 6XX XXX XXX', 'Orange Money' => '+237 6XX XXX XXX', 'Name' => $name]],
            ['Zambia', 'Airtel / MTN Money', ['Airtel Money' => '+260 9XX XXX XXX', 'MTN MoMo' => '+260 9XX XXX XXX', 'Name' => $name]],
            ['Malawi', 'Airtel Money / TNM Mpamba', ['Airtel Money' => '+265 9XX XXX XXX', 'TNM Mpamba' => '+265 8XX XXX XXX', 'Name' => $name]],
            ['South Africa', 'Bank Transfer (EFT)', ['Bank' => 'XXXX Bank', 'Account name' => $name, 'Account number' => 'XXXXXXXXXX', 'Branch code' => 'XXXXXX']],

            ['others', 'USDT (TRC20)', ['Network' => 'TRON (TRC20) only', 'Wallet address' => 'TXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX']],
            ['others', 'Bitcoin (BTC)', ['Network' => 'Bitcoin', 'Wallet address' => 'bc1XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX']],
            ['others', 'Skrill', ['Skrill email' => 'XXXX@XXXX.com', 'Name' => $name]],
        ];
    }
}
