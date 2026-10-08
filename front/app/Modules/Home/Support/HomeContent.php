<?php

namespace App\Modules\Home\Support;

use App\Models\GameCat;

class HomeContent
{
    public const SLUG = 'homeslug';

    /** The homepage tip category managed in the admin (hero, SEO, on-page copy). */
    public static function page(): ?GameCat
    {
        return once(fn () => GameCat::query()->where('slug', self::SLUG)->first());
    }
}
