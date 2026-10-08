<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'advert_email',
        'contact_email',
        'contact_phone',
        'whatsapp_no',
        'payment_proof_instruction',
        'homepage_results_plan_category_id',
        'homepage_results_week_date',
        'whatsapp_link',
        'facebook_link',
        'tiktok_link',
        'instagram_link',
        'linkedin_link',
        'telegram_link',
        'telegram_admin_link',
        'telegram_admin_username',
        'twitter_link',
        'skype_link',
        'logo',
        'favicon',
        'logo_width',
        'logo_height',
    ];

    protected $casts = [
        'homepage_results_week_date' => 'date',
    ];

    /** @return BelongsTo<PlanCategory, $this> */
    public function homepageResultsPlanCategory(): BelongsTo
    {
        return $this->belongsTo(PlanCategory::class, 'homepage_results_plan_category_id');
    }
}
