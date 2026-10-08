<?php

use App\Http\Controllers\MediaAssetController;
use App\Http\Controllers\QuickPickPredictionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public media proxy — front uses this instead of media.api-sports.io
Route::get('/media/{type}/{id}', [MediaAssetController::class, 'show'])
    ->where(['type' => 'teams|players|leagues', 'id' => '[0-9]+(?:\.(?:webp|png|jpe?g|gif))?'])
    ->name('media.asset');

// Quick Edit modal — free + VIP predictions (auth checked in controller → JSON 401)
Route::get('/admin/api/fixtures/{id}/predictions', [QuickPickPredictionController::class, 'show']);
Route::post('/admin/api/fixtures/{id}/predictions', [QuickPickPredictionController::class, 'store']);
Route::get('/admin/api/fixtures/{id}/vip/predictions', [QuickPickPredictionController::class, 'showVip']);
Route::post('/admin/api/fixtures/{id}/vip/predictions', [QuickPickPredictionController::class, 'storeVip']);
// Quick edit endpoint used by the global modal (example: update fixture basic fields)

// Paystack init endpoint (called by frontend to get authorization_url)
Route::post('/payments/paystack/init', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'amount' => 'required|numeric',
        'currency' => 'required|string',
        'email' => 'nullable|email',
        'metadata' => 'nullable',
    ]);

    // Store a pending payment record
    $payment = \App\Models\Payment::create([
        'user_id' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null,
        'amount' => (int) $data['amount'],
        'currency' => $data['currency'],
        'status' => 'pending',
        'metadata' => $data['metadata'] ?? null,
    ]);

    // Initialize with Paystack (env first, then payment_details.json from Filament)
    $secret = (function (): string {
        $fromEnv = (string) env('PAYSTACK_SECRET_KEY', '');
        if ($fromEnv !== '') {
            return $fromEnv;
        }
        try {
            $details = app(\App\Services\PaymentDetailsFileRepository::class)->read();

            return (string) ($details['paystack_secret_key'] ?? '');
        } catch (\Throwable) {
            return '';
        }
    })();
    $initPayload = [
        'amount' => $data['amount'],
        'currency' => $data['currency'],
        'email' => $data['email'] ?? '',
        'reference' => 'p' . $payment->id . '-' . time(),
        'callback_url' => env('APP_URL') . '/payments/paystack/callback',
        'metadata' => $data['metadata'] ?? null,
    ];

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.paystack.co/transaction/initialize');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($initPayload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $secret,
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resp = json_decode($res, true);

        if ($httpcode !== 200 || empty($resp['data']['authorization_url'])) {
            return response()->json(['error' => 'Paystack init failed', 'resp' => $resp], 500);
        }

        // Save the payment reference
        $payment->payment_id = $initPayload['reference'];
        $payment->save();

        return response()->json(['authorization_url' => $resp['data']['authorization_url'], 'reference' => $initPayload['reference']]);
    } catch (\Throwable $e) {
        return response()->json(['error' => 'Initialization error', 'message' => $e->getMessage()], 500);
    }
});

// Paystack webhook
Route::post('/payments/paystack/webhook', function (\Illuminate\Http\Request $request) {
    $secret = (function (): string {
        $fromEnv = (string) env('PAYSTACK_SECRET_KEY', '');
        if ($fromEnv !== '') {
            return $fromEnv;
        }
        try {
            $details = app(\App\Services\PaymentDetailsFileRepository::class)->read();

            return (string) ($details['paystack_secret_key'] ?? '');
        } catch (\Throwable) {
            return '';
        }
    })();
    $signature = $request->header('x-paystack-signature');
    $payload = $request->getContent();

    // Verify signature
    if ($signature !== hash_hmac('sha512', $payload, $secret)) {
        return response('Invalid signature', 400);
    }

    $data = $request->json()->all();
    $event = $data['event'] ?? null;
    $ref = $data['data']['reference'] ?? null;
    $gatewayId = $data['data']['id'] ?? null;
    $status = $data['data']['status'] ?? null;

    if ($ref) {
        $payment = \App\Models\Payment::where('payment_id', $ref)->first();
        if ($payment) {
            $payment->gateway_id = $gatewayId;
            $payment->status = $status;
            $payment->metadata = $data['data'] ?? $payment->metadata;
            $payment->save();

            // If verified, update user subscription or mark as paid (example)
            if ($status === 'success') {
                // attach to user if available
                if ($payment->user_id) {
                    \Illuminate\Support\Facades\DB::table('user_payments')->insertOrIgnore([
                        'user_id' => $payment->user_id,
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'status' => 'paid',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    return response('OK', 200);
});


Route::get('mark-result', [App\Http\Controllers\FreeTipsController::class, 'MarkResult'])->name('mark.result');

Route::get('load-acca-tips', [App\Http\Controllers\FreeTipsController::class, 'loadAccaTips'])->name('load.acca.tips');
Route::get('load-free-tips', [App\Http\Controllers\FreeTipsController::class, 'loadFreeTips'])->name('load.free.tips');

Route::get('load-banker-tips', [App\Http\Controllers\FreeTipsController::class, 'loadBankerTips'])->name('load.banker.tips');
