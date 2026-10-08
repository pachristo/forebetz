<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class APIprediction extends Model
{
    protected $table = 'api_predictions';

    protected $fillable = [
       'id', 'match_id', 'pred', 'pred_data', 'auto_predict', 'predicted', 'h2h_pred', 'created_at', 'updated_at', 'home_form', 'away_form'
    ];
    protected $casts=[

        'pred_data'=>'array',
        'h2h_pred'=>'array'
    ]
;

public static function boot()
{
    parent::boot();

    // Use a model event to fix 'odd_data' during initialization
    static::retrieved(function ($model) {
        if (is_string($model->pred_data)) {
            $model->pred_data = json_decode($model->pred_data, true) ?? [];
        }
    });
}
    public function match()
    {
        return $this->belongsTo(\App\Models\Fixture::class, 'match_id', 'match_id');
    }
}
