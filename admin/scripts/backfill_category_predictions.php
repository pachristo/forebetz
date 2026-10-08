<?php

/**
 * One-off backfill: ensure up to $target predictions per game_cats category for a date.
 * Uses existing fixture predictions as donors — does not create categories.
 *
 * Usage: docker exec WinningPredict_admin php scripts/backfill_category_predictions.php --date=2026-06-08 --target=3
 *        docker exec WinningPredict_admin php scripts/backfill_category_predictions.php tomorrow --target=3
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\GameCat;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

$target = 3;
$date = \Carbon\Carbon::today()->format('Y-m-d');

foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--target=')) {
        $target = max(1, (int) substr($arg, 9));
    } elseif (str_starts_with($arg, '--date=')) {
        $date = substr($arg, 7);
    } elseif ($arg === 'today') {
        $date = \Carbon\Carbon::today()->format('Y-m-d');
    } elseif ($arg === 'tomorrow') {
        $date = \Carbon\Carbon::tomorrow()->format('Y-m-d');
    }
}

$legal = [
    'homeslug',
    'about-us', 'policy', 'disclaimer', 'refund-policy', 'terms-and-condition', 'partners',
];

$cats = GameCat::query()
    ->where(function ($q) {
        $q->whereNull('status')->orWhere('status', 'published');
    })
    ->whereNotIn('slug', $legal)
    ->where('slug', '!=', 'homeslug')
    ->orderBy('title')
    ->get(['id', 'title', 'slug', 'prediction_slug', 'cat_type']);

$fixtureIds = DB::table('fixtures')
    ->whereDate('match_date', $date)
    ->whereIn('match_id', function ($q) {
        $q->select('match_id')
            ->from('predictions')
            ->whereNotNull('tips')
            ->where('tips', '!=', '');
    })
    ->orderBy('match_time')
    ->pluck('match_id')
    ->all();

if ($fixtureIds === []) {
    echo "No fixtures with predictions for {$date}. Run tip:predict first.\n";
    exit(1);
}

$donorFor = static function (int $matchId): ?Prediction {
    return Prediction::query()
        ->where('match_id', $matchId)
        ->whereNotNull('tips')
        ->where('tips', '!=', '')
        ->orderByDesc('prob')
        ->orderBy('odds')
        ->first();
};

$buildRow = static function (string $type, Prediction $donor): ?array {
    $tips = (string) $donor->tips;
    $odds = (float) $donor->odds;
    $prob = (float) $donor->prob;

    switch ($type) {
        case 'banker':
            if ($prob < 60 || $odds > 1.5 || $odds < 1.12) {
                return null;
            }
            break;
        case 'super_single':
            if ($odds < 1.3 || $odds > 2.6) {
                return null;
            }
            break;
        case 'weh':
            $tips = match ($donor->type) {
                'home_win', '1_5_goal', '2_5_goal' => 'HWEH',
                'away_win' => 'AWEH',
                default => str_contains(strtolower($tips), 'away') ? 'AWEH' : 'HWEH',
            };
            break;
        case '0_5_goal':
            $tips = '+0.5 HT';
            $odds = max(1.15, min($odds, 1.65));
            break;
        case 'correct_score':
            $tips = match ($donor->type) {
                'home_win' => '1-0',
                'away_win' => '0-1',
                'draw' => '1-1',
                'btts' => '2-1',
                default => '1-1',
            };
            $odds = max(6.0, min($odds * 3, 15.0));
            break;
        case '1_5_goal':
            $tips = str_contains($tips, '-') ? '-1.5' : '+1.5';
            break;
        case '2_5_goal':
            $tips = str_contains($tips, '-') ? '-2.5' : '+2.5';
            break;
        case '3_5_goal':
            $tips = str_contains($tips, '-') ? '-3.5' : '+3.5';
            break;
        case 'btts':
            $tips = in_array(strtolower($tips), ['yes', 'no', 'gg', 'ng'], true)
                ? $tips
                : (str_contains(strtolower($tips), 'no') ? 'no' : 'yes');
            break;
        case 'double_chance':
            if (! in_array(strtolower($tips), ['1x', 'x2', '12', '1X', 'X2'], true)) {
                $tips = match ($donor->type) {
                    'home_win' => '1X',
                    'away_win' => 'X2',
                    'draw' => '1X',
                    default => '1X',
                };
            }
            break;
        case 'draw':
            if ($donor->type !== 'draw') {
                $tips = 'X';
            }
            break;
        case 'home_win':
            if ($donor->type !== 'home_win') {
                $tips = '1';
            }
            break;
        case 'away_win':
            if ($donor->type !== 'away_win') {
                $tips = '2';
            }
            break;
    }

    if ($odds < 1.12) {
        return null;
    }

    return [
        'match_id' => $donor->match_id,
        'type' => $type,
        'tips' => $tips,
        'odds' => $odds,
        'prob' => $prob > 0 ? $prob : round((1 / $odds) * 100, 2),
        'vip_type' => 'regular',
    ];
};

echo "Backfilling up to {$target} predictions per category for {$date}\n\n";

foreach ($cats as $cat) {
    $type = trim((string) ($cat->prediction_slug ?: $cat->cat_type));
    if ($type === '') {
        continue;
    }

    $existing = (int) DB::table('predictions')
        ->join('fixtures', 'predictions.match_id', '=', 'fixtures.match_id')
        ->whereDate('fixtures.match_date', $date)
        ->where('predictions.type', $type)
        ->whereNotNull('predictions.tips')
        ->where('predictions.tips', '!=', '')
        ->count();

    $needed = max(0, $target - $existing);
    $created = 0;

    if ($needed > 0) {
    foreach ($fixtureIds as $matchId) {
        if ($created >= $needed) {
            break;
        }

        $hasType = Prediction::query()
            ->where('match_id', $matchId)
            ->where('type', $type)
            ->whereNotNull('tips')
            ->where('tips', '!=', '')
            ->exists();

        if ($hasType) {
            continue;
        }

        $donor = $donorFor($matchId);
        if ($donor === null) {
            continue;
        }

        $row = $buildRow($type, $donor);
        if ($row === null) {
            continue;
        }

        Prediction::updateOrCreate(
            ['match_id' => $matchId, 'type' => $type],
            $row,
        );
        $created++;
    }
    }

    $trimmed = 0;
    $keepIds = DB::table('predictions')
        ->join('fixtures', 'predictions.match_id', '=', 'fixtures.match_id')
        ->whereDate('fixtures.match_date', $date)
        ->where('predictions.type', $type)
        ->whereNotNull('predictions.tips')
        ->where('predictions.tips', '!=', '')
        ->orderByDesc('predictions.prob')
        ->orderBy('predictions.odds')
        ->limit($target)
        ->pluck('predictions.id')
        ->all();

    if ($keepIds !== []) {
        $trimmed = DB::table('predictions')
            ->join('fixtures', 'predictions.match_id', '=', 'fixtures.match_id')
            ->whereDate('fixtures.match_date', $date)
            ->where('predictions.type', $type)
            ->whereNotIn('predictions.id', $keepIds)
            ->delete();
    }

    $final = count($keepIds);
    $suffix = $created > 0 ? " (+{$created})" : '';
    if ($trimmed > 0) {
        $suffix .= " (-{$trimmed} trimmed)";
    }
    echo str_pad($cat->slug, 36)." | {$type} | {$final}{$suffix}\n";
}

echo "\nDone.\n";
