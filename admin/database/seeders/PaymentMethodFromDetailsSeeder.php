<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\SiteConfiguration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PaymentMethodFromDetailsSeeder extends Seeder
{
    /**
     * Import payment methods + site contact from payment_details.json (legacy pay.blade.php).
     */
    public function run(): void
    {
        $jsonPath = public_path('payment_details.json');
        if (! is_readable($jsonPath)) {
            $this->command?->error('payment_details.json not found at '.$jsonPath);

            return;
        }

        $raw = file_get_contents($jsonPath);
        $data = json_decode($raw ?: '{}', true);
        if (! is_array($data)) {
            $this->command?->error('Invalid payment_details.json');

            return;
        }

        $this->copyPaymentIcons();
        $this->syncSiteConfiguration($data);
        $this->syncPaymentMethods($data);

        $this->command?->info('Payment methods and site configuration synced from payment_details.json.');
    }

    private function copyPaymentIcons(): void
    {
        $targetDir = storage_path('app/public/payment-methods');
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $root = dirname(base_path());

        $sources = [
            'mpesa.webp' => [
                $root.'/old/assets/mpesa.webp',
                $root.'/front/public/assets/payment/mpesa.png',
            ],
            'paystack.png' => [
                $root.'/old/assets/paystack.png',
                $root.'/front/public/assets/soccer/images/paystack_online.png',
            ],
            'bank.png' => [
                $root.'/old/assets/bank.png',
                $root.'/front/public/assets/images/bank_transfer.png',
            ],
            'ghcmobile.png' => [
                $root.'/old/assets/images/ghcmobile.png',
                $root.'/front/public/assets/images/ghcmobile.png',
            ],
            'xaf.png' => [
                $root.'/old/assets/xaf.png',
                $root.'/front/public/assets/xaf.png',
            ],
            'skrill.jpg' => [
                $root.'/old/assets/skrill.jpg',
                $root.'/front/public/assets/images/skrill.png',
            ],
            'bitcoin.png' => [
                $root.'/old/assets/bitcoin.png',
            ],
            'usdt.png' => [
                $root.'/old/assets/usdt.png',
            ],
        ];

        foreach ($sources as $filename => $candidates) {
            $copied = false;
            foreach ($candidates as $source) {
                if (! is_readable($source)) {
                    continue;
                }
                File::copy($source, $targetDir.DIRECTORY_SEPARATOR.$filename);
                $copied = true;
                break;
            }
            if (! $copied) {
                $this->command?->warn("Icon missing, skipped: {$filename}");
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncSiteConfiguration(array $data): void
    {
        $config = SiteConfiguration::query()->first();
        if ($config === null) {
            $config = new SiteConfiguration;
        }

        $config->fill([
            'contact_email' => (string) ($data['contactmail'] ?? $config->contact_email ?? ''),
            'advert_email' => (string) ($data['advertmail'] ?? $data['contactmail'] ?? $config->advert_email ?? ''),
            'contact_phone' => (string) ($data['callno'] ?? $config->contact_phone ?? ''),
            'whatsapp_no' => (string) ($data['whatsappno'] ?? $config->whatsapp_no ?? ''),
            'payment_proof_instruction' => $this->paymentProofTemplate(),
            'whatsapp_link' => (string) ($data['whatsapplink'] ?? $config->whatsapp_link ?? ''),
            'telegram_link' => (string) ($data['telegramlink'] ?? $config->telegram_link ?? ''),
        ]);

        $config->save();
    }

    /**
     * Default template stored in Site Configuration (editable in admin).
     */
    private function paymentProofTemplate(): string
    {
        return "After you're done with the payment, kindly send the payment proof to our email {contact_email} "
            .'or WhatsApp phone number {whatsapp_no}. Your account will be activated instantly.';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncPaymentMethods(array $data): void
    {
        $proofText = $this->paymentProofTemplate();
        $methods = [];

        $mpesaCountries = [
            'Kenya',
            'Uganda',
            'Tanzania',
            'Rwanda',
            'South Sudan',
            'Zambia',
            'Malawi',
        ];
        $mpesaInstructions = $this->fieldListHtml([
            'MPESA NO.' => (string) ($data['mpesa_no'] ?? ''),
            'MPESA NAME' => (string) ($data['mpesa_name'] ?? ''),
        ]);

        foreach ($mpesaCountries as $country) {
            $methods[] = [
                'name' => 'Payment with M-Pesa',
                'country' => $country,
                'image' => 'payment-methods/mpesa.webp',
                'text' => $proofText,
                'instructions' => $mpesaInstructions,
            ];
        }

        $methods[] = [
            'name' => 'Payment with Paystack',
            'country' => 'Nigeria',
            'image' => 'payment-methods/paystack.png',
            'text' => $proofText,
            'instructions' => null,
        ];

        $methods[] = [
            'name' => 'Bank Transfer',
            'country' => 'Nigeria',
            'image' => 'payment-methods/bank.png',
            'text' => $proofText,
            'instructions' => $this->fieldListHtml([
                'Acct Name' => (string) ($data['bankaccountname'] ?? ''),
                'Acct No' => (string) ($data['bankaccountno'] ?? ''),
                'Bank' => (string) ($data['bankname'] ?? ''),
            ]),
        ];

        $methods[] = [
            'name' => 'Payment with MOMO',
            'country' => 'Ghana',
            'image' => 'payment-methods/ghcmobile.png',
            'text' => $proofText,
            'instructions' => $this->fieldListHtml([
                'MOMO NO' => (string) ($data['ghana_no'] ?? ''),
                'MOMO NAME' => (string) ($data['ghana_name'] ?? ''),
            ]),
        ];

        $methods[] = [
            'name' => 'Payment with MOMO',
            'country' => 'Cameroon',
            'image' => 'payment-methods/xaf.png',
            'text' => $proofText,
            'instructions' => $this->fieldListHtml([
                'MOMO NO' => (string) ($data['cameroon_no'] ?? ''),
                'MOMO NAME' => (string) ($data['cameroon_name'] ?? ''),
            ]),
        ];

        if (filled($data['south_africa_bank_bank'] ?? null) || filled($data['south_africa_bank_name'] ?? null)) {
            $methods[] = [
                'name' => 'Bank Transfer',
                'country' => 'South Africa',
                'image' => 'payment-methods/bank.png',
                'text' => $proofText,
                'instructions' => $this->fieldListHtml([
                    'Bank' => (string) ($data['south_africa_bank_bank'] ?? ''),
                    'Acct Name' => (string) ($data['south_africa_bank_name'] ?? ''),
                    'Acct No' => (string) ($data['south_africa_bank_no'] ?? ''),
                ]),
            ];
        }

        if (filled($data['perfect'] ?? null)) {
            $methods[] = [
                'name' => 'Payment with Perfect Money',
                'country' => 'others',
                'image' => 'payment-methods/skrill.jpg',
                'text' => $proofText,
                'instructions' => $this->fieldListHtml([
                    'Account' => (string) $data['perfect'],
                ]),
            ];
            $methods[] = [
                'name' => 'Payment with Perfect Money',
                'country' => 'South Africa',
                'image' => 'payment-methods/skrill.jpg',
                'text' => $proofText,
                'instructions' => $this->fieldListHtml([
                    'Account' => (string) $data['perfect'],
                ]),
            ];
        }

        if (filled($data['skrill'] ?? null)) {
            $methods[] = [
                'name' => 'Payment with Skrill',
                'country' => 'others',
                'image' => 'payment-methods/skrill.jpg',
                'text' => $proofText,
                'instructions' => $this->fieldListHtml([
                    'Address' => (string) $data['skrill'],
                ]),
            ];
        }

        if (filled($data['bitcoin'] ?? null)) {
            $methods[] = [
                'name' => 'Payment with Bitcoin',
                'country' => 'others',
                'image' => 'payment-methods/bitcoin.png',
                'text' => $proofText,
                'instructions' => $this->fieldListHtml([
                    'Address' => (string) $data['bitcoin'],
                ]),
            ];
        }

        if (filled($data['usdt'] ?? null)) {
            $methods[] = [
                'name' => 'Payment with USDT (TRC 20)',
                'country' => 'others',
                'image' => 'payment-methods/usdt.png',
                'text' => $proofText,
                'instructions' => $this->fieldListHtml([
                    'Address' => (string) $data['usdt'],
                ]),
            ];
        }

        foreach ($methods as $payload) {
            PaymentMethod::query()->updateOrCreate(
                [
                    'name' => $payload['name'],
                    'country' => $payload['country'],
                ],
                [
                    'image' => $payload['image'],
                    'text' => $payload['text'],
                    'instructions' => $payload['instructions'],
                ]
            );
        }
    }

    /**
     * @param  array<string, string>  $fields
     */
    private function fieldListHtml(array $fields): string
    {
        $items = [];
        foreach ($fields as $label => $value) {
            $value = trim($value);
            if ($value === '') {
                continue;
            }
            $items[] = '<li><strong>'.e($label).':</strong> '.e($value).'</li>';
        }

        if ($items === []) {
            return '';
        }

        return '<ul>'.implode('', $items).'</ul>';
    }
}
