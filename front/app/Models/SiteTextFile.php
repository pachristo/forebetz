<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteTextFile extends Model
{
    public const KEY_ROBOTS = 'robots';

    public const KEY_ADS = 'ads';

    public const KEY_LLMS = 'llms';

    protected $fillable = [
        'key',
        'label',
        'content',
    ];

    /**
     * @return array<string, string>
     */
    public static function keyLabels(): array
    {
        return [
            self::KEY_ROBOTS => 'robots.txt',
            self::KEY_ADS => 'ads.txt',
            self::KEY_LLMS => 'llms.txt',
        ];
    }

    public static function recordFor(string $key): self
    {
        $labels = self::keyLabels();
        if (! isset($labels[$key])) {
            throw new \InvalidArgumentException('Unknown site text file key: '.$key);
        }

        return static::query()->firstOrCreate(
            ['key' => $key],
            [
                'label' => $labels[$key],
                'content' => '',
            ],
        );
    }
}
