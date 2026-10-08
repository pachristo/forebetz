<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class HomeReadyInvestTicket extends Model
{
    protected $fillable = [
        'sort_order',
        'result_date',
        'outcome',
        'day_label',
        'display_date_override',
        'is_visible',
    ];

    protected $casts = [
        'result_date' => 'date',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function displayDay(): string
    {
        $raw = $this->day_label;
        if (is_string($raw) && trim($raw) !== '') {
            return strtoupper(trim($raw));
        }

        /** @var CarbonInterface $d */
        $d = $this->result_date;

        return strtoupper($d->format('l'));
    }

    public function displayDateLine(): string
    {
        $raw = $this->display_date_override;
        if (is_string($raw) && trim($raw) !== '') {
            return trim($raw);
        }

        /** @var CarbonInterface $d */
        $d = $this->result_date;

        return $d->format('n/j/y');
    }

    public function isWon(): bool
    {
        return $this->outcome === 'won';
    }
}
