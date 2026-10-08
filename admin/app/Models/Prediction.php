<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prediction extends Model
{
    protected $fillable = ['match_id', 'type', 'vip_type', 'tips', 'odds','winning_status','prob','status'];

    public $incrementing = false;

    protected $keyType = 'string';

    public function fixture()
    {
        return $this->belongsTo(Fixture::class, 'match_id', 'match_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::random(9);
            }
        });
    }
}
