<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Replace site contacts + payment_methods from winningpredict payment_details / legacy pay.blade.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        $proof = "After you're done with the payment, kindly send the payment proof to our email {contact_email} "
            .'or WhatsApp phone number {whatsapp_no}. Your account will be activated instantly.';

        $now = now();

        if (DB::table('site_configurations')->exists()) {
            DB::table('site_configurations')->update([
                'contact_email' => 'Winningpredict001@gmail.com',
                'advert_email' => 'Winningpredict001@gmail.com',
                'contact_phone' => '+2348052878581',
                'whatsapp_no' => '+2348052878581',
                'whatsapp_link' => 'https://wa.me/message/HD6XO3H4LQ6XB1',
                'telegram_link' => 'https://t.me/+ll-iIUebS1Y4YzE0',
                'facebook_link' => null,
                'payment_proof_instruction' => $proof,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('site_configurations')->insert([
                'contact_email' => 'Winningpredict001@gmail.com',
                'advert_email' => 'Winningpredict001@gmail.com',
                'contact_phone' => '+2348052878581',
                'whatsapp_no' => '+2348052878581',
                'whatsapp_link' => 'https://wa.me/message/HD6XO3H4LQ6XB1',
                'telegram_link' => 'https://t.me/+ll-iIUebS1Y4YzE0',
                'payment_proof_instruction' => $proof,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('payment_methods')->delete();

        $rows = [];

        $mpesaCountries = [
            'Kenya',
            'Uganda',
            'Tanzania',
            'South Sudan',
            'Rwanda',
            'Zambia',
            'Malawi',
        ];
        foreach ($mpesaCountries as $country) {
            $rows[] = $this->method(
                'Payment with M-Pesa',
                'payment-methods/mpesa.webp',
                $country,
                $proof,
                '<ul>'
                .'<li><strong>MPESA NO.:</strong> +254700692793</li>'
                .'<li><strong>MPESA NAME:</strong> GORGE OPIYO</li>'
                .'</ul>',
                $now
            );
        }

        $rows[] = $this->method(
            'Payment with Paystack',
            'payment-methods/paystack.png',
            'Nigeria',
            $proof,
            null,
            $now
        );

        $rows[] = $this->method(
            'Bank Transfer',
            'payment-methods/bank.png',
            'Nigeria',
            $proof,
            '<ul>'
            .'<li><strong>Acct Name:</strong> Glory Ezichi Maduka</li>'
            .'<li><strong>Acct No:</strong> 1865055613</li>'
            .'<li><strong>Bank:</strong> Access</li>'
            .'</ul>',
            $now
        );

        $rows[] = $this->method(
            'Bank Transfer',
            'payment-methods/bank.png',
            'South Africa',
            $proof,
            '<ul>'
            .'<li><strong>Bank:</strong> Convert Rands To:</li>'
            .'<li><strong>Pay via:</strong> Skrill, Bitcoin Or USDT</li>'
            .'<li><strong>Skrill:</strong> Madukaglory001@gmail.com</li>'
            .'<li><strong>Bitcoin:</strong> 1ERfaSax8tEBJ6ASnoQ8hCqMfwV2PrhWkF</li>'
            .'<li><strong>USDT (TRC20):</strong> TTAADkU1cbh2uwGyhaZceb1hTAxFDreA3r</li>'
            .'</ul>',
            $now
        );

        $rows[] = $this->method(
            'Payment with Perfect Money',
            'payment-methods/bank.png',
            'South Africa',
            $proof,
            '<ul><li><strong>Address:</strong> U41058382</li></ul>',
            $now
        );

        $rows[] = $this->method(
            'Payment with MOMO',
            'payment-methods/xaf.png',
            'Cameroon',
            $proof,
            '<ul>'
            .'<li><strong>MOMO NO:</strong> 678832736</li>'
            .'<li><strong>MOMO NAME:</strong> PROMISE AMADI</li>'
            .'</ul>',
            $now
        );

        // USD / other countries (legacy pay.blade else-block)
        $rows[] = $this->method(
            'Payment with Bitcoin',
            'payment-methods/bitcoin.png',
            'others',
            $proof,
            '<ul><li><strong>Address:</strong> 1ERfaSax8tEBJ6ASnoQ8hCqMfwV2PrhWkF</li></ul>',
            $now
        );

        $rows[] = $this->method(
            'Payment with Skrill',
            'payment-methods/skrill.jpg',
            'others',
            $proof,
            '<ul><li><strong>Address:</strong> Madukaglory001@gmail.com</li></ul>',
            $now
        );

        $rows[] = $this->method(
            'Payment with USDT (TRC 20)',
            'payment-methods/usdt.png',
            'others',
            $proof,
            '<ul><li><strong>Address:</strong> TTAADkU1cbh2uwGyhaZceb1hTAxFDreA3r</li></ul>',
            $now
        );

        $rows[] = $this->method(
            'Payment with Perfect Money',
            'payment-methods/bank.png',
            'others',
            $proof,
            '<ul><li><strong>Address:</strong> U41058382</li></ul>',
            $now
        );

        DB::table('payment_methods')->insert($rows);
    }

    public function down(): void
    {
        // irreversible contact/payment reset
    }

    /**
     * @return array<string, mixed>
     */
    private function method(
        string $name,
        ?string $image,
        string $country,
        string $text,
        ?string $instructions,
        $now
    ): array {
        return [
            'name' => $name,
            'image' => $image,
            'text' => $text,
            'instructions' => $instructions,
            'country' => $country,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
};
