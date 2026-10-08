<?php

namespace App\Services;

use JsonException;
use RuntimeException;

class PaymentDetailsFileRepository
{
    public function path(): string
    {
        $configured = config('payment.details_json_path');
        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return public_path('payment_details.json');
    }

    /**
     * @return array<string, mixed>
     */
    public function read(): array
    {
        $path = $this->path();
        if (! is_readable($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            return [];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  string|null  $secretKey  Omit or null to leave existing secret unchanged; empty string clears it.
     */
    public function updatePaystackKeys(string $publicKey, ?string $secretKey = null): void
    {
        $path = $this->path();
        $dir = dirname($path);
        if (! is_dir($dir)) {
            if (! mkdir($dir, 0755, true) && ! is_dir($dir)) {
                throw new RuntimeException("Cannot create directory: {$dir}");
            }
        }

        $data = $this->read();
        $data['paystack_public_key'] = $publicKey;
        if ($secretKey !== null) {
            if ($secretKey === '') {
                $data['paystack_secret_key'] = '';
            } else {
                $data['paystack_secret_key'] = $secretKey;
            }
        }

        try {
            $encoded = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (JsonException $e) {
            throw new RuntimeException('Failed to encode payment details: '.$e->getMessage(), 0, $e);
        }

        if (file_put_contents($path, $encoded, LOCK_EX) === false) {
            throw new RuntimeException("Cannot write file: {$path}");
        }
    }
}
