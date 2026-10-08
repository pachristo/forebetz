<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeReadyInvestSetting extends Model
{
    protected $fillable = [
        'heading',
        'body',
        'vip_strip_title',
        'telegram_cta',
        'telegram_url',
    ];

    /** @return array<string, string> */
    public static function defaultAttributes(): array
    {
        return [
            'heading' => 'Ready TO INVEST',
            'body' => 'The investment scheme is recommended for serious bettors who believe in the smart way of making profit steadily. In this scheme, we provide odds within the range of 1.50 - 2.00 odds daily.',
            'vip_strip_title' => 'VIP RESULT',
            'telegram_cta' => 'Join Us Telegram',
            'telegram_url' => null,
        ];
    }

    protected $casts = [
        //
    ];
}
