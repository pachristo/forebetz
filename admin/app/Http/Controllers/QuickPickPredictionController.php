<?php

namespace App\Http\Controllers;

use App\Models\PlanCategory;
use App\Models\Prediction;
use App\Services\PredictionOddsBackfillService;
use App\Support\PredictionTipOptions;
use App\Support\QuickPickFields;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Quick Edit modal: load/save free + VIP {@code predictions} by fixture match_id.
 */
final class QuickPickPredictionController extends Controller
{
    public function __construct(
        private readonly PredictionOddsBackfillService $oddsBackfill,
    ) {}

    public function show(string $matchId): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Fill any tip rows that already exist without odds (uses api_odds; fetches if needed).
        $this->oddsBackfill->fillMissingForMatch($matchId);

        $data = [];

        foreach (Prediction::query()->where('match_id', $matchId)->get(['type', 'tips', 'odds']) as $pred) {
            $type = (string) ($pred->type ?? '');
            if ($type === '' || str_starts_with($type, 'v')) {
                continue;
            }
            // Normalize tip labels for selects (e.g. Over 3.5 → +3.5, yes → gg).
            $tips = trim((string) ($pred->tips ?? ''));
            if ($tips !== '') {
                $canonical = PredictionTipOptions::canonicalKey($tips);
                if ($canonical !== '') {
                    $tips = $canonical;
                }
            }
            if ($type === 'btts') {
                $tips = match (strtolower($tips)) {
                    'yes', 'y', 'bts', 'btts', 'btts_yes' => 'gg',
                    'no', 'n', 'btts_no' => 'ng',
                    default => $tips,
                };
            }
            // Legacy o35g → single 3_5_goal field in the modal.
            if ($type === 'o35g') {
                $type = '3_5_goal';
            }
            // Prefer Over when both legacy rows somehow still exist.
            if ($type === '3_5_goal' && ($data['3_5_goal'] ?? '') !== '' && str_starts_with((string) $data['3_5_goal'], '-')) {
                if (str_starts_with($tips, '+')) {
                    $data[$type] = $tips;
                    $data[QuickPickFields::oddsPayloadKey($type)] = $pred->odds ?? '';
                }
                continue;
            }
            $data[$type] = $tips;
            $data[QuickPickFields::oddsPayloadKey($type)] = $pred->odds ?? '';
        }

        // Straight Win field is keyed as home_win — surface away_win when home is empty.
        if (($data['home_win'] ?? '') === '' && ($data['away_win'] ?? '') !== '') {
            $data['home_win'] = $data['away_win'];
            $data[QuickPickFields::oddsPayloadKey('home_win')] = $data[QuickPickFields::oddsPayloadKey('away_win')] ?? '';
        }

        return response()->json([
            'ok' => true,
            'predictions' => $data,
        ]);
    }

    public function store(Request $request, string $matchId): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();

        if (! is_array($payload)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        // Resolve Home/Away together first. Empty home_win must not wipe a filled away_win
        // (DOM order used to save away_win then delete both via storeStraightWin).
        $straightSeen = false;
        $straightTips = '';
        $straightOdds = '';
        // away_win first, then home_win so the Straight Win field wins when both are present.
        foreach (['away_win', 'home_win'] as $straightKey) {
            if (! array_key_exists($straightKey, $payload)) {
                continue;
            }
            $straightSeen = true;
            $t = trim((string) ($payload[$straightKey] ?? ''));
            $o = trim((string) ($payload[QuickPickFields::oddsPayloadKey($straightKey)] ?? ''));
            if ($t === '' && $o === '') {
                continue;
            }
            if ($t === '' && $o !== '') {
                $t = $o;
            }
            if ($straightKey === 'away_win' && $t !== '') {
                $t = '2';
            }
            $straightTips = $t;
            if ($o !== '') {
                $straightOdds = $o;
            }
        }
        if ($straightSeen) {
            if ($straightTips !== '' && $this->oddsBackfill->isMissingOdds($straightOdds)) {
                $straightOdds = $this->oddsBackfill->resolveForTip($matchId, $straightTips);
            }
            $this->storeStraightWin($matchId, $straightTips, $straightOdds);
        }

        foreach ($payload as $key => $value) {
            $key = (string) $key;
            if ($key === '' || QuickPickFields::isOddsPayloadKey($key) || str_starts_with($key, 'v')) {
                continue;
            }

            $type = $key;
            $tips = trim((string) $value);
            $odds = trim((string) ($payload[QuickPickFields::oddsPayloadKey($type)] ?? ''));

            // Handled above as one Straight Win write (home_win or away_win).
            if ($type === 'home_win' || $type === 'away_win') {
                continue;
            }

            // Legacy Over-only key → single O/U 3.5 type.
            if ($type === 'o35g') {
                $type = '3_5_goal';
            }

            if ($tips === '' && $odds === '') {
                $deleteTypes = [$type];
                if ($type === '3_5_goal') {
                    $deleteTypes[] = 'o35g';
                }
                Prediction::query()
                    ->where('match_id', $matchId)
                    ->whereIn('type', $deleteTypes)
                    ->delete();

                continue;
            }

            if ($tips === '' && $odds !== '') {
                $tips = $odds;
            }

            if ($tips !== '' && $this->oddsBackfill->isMissingOdds($odds)) {
                $odds = $this->oddsBackfill->resolveForTip($matchId, $tips);
            }

            Prediction::updateOrCreate(
                ['match_id' => $matchId, 'type' => $type],
                [
                    'match_id' => $matchId,
                    'type' => $type,
                    'tips' => $tips,
                    'odds' => $odds,
                    'vip_type' => 'regular',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // Keep a single 3_5_goal row — drop legacy o35g after save.
            if ($type === '3_5_goal') {
                Prediction::query()
                    ->where('match_id', $matchId)
                    ->where('type', 'o35g')
                    ->delete();
            }
        }

        $this->oddsBackfill->fillMissingForMatch($matchId);

        return response()->json(['ok' => true]);
    }

    public function showVip(string $matchId): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->oddsBackfill->fillMissingForMatch($matchId);

        $data = [];
        $vipPredictions = Prediction::query()
            ->where('match_id', $matchId)
            ->where('type', 'like', 'v%')
            ->get(['type', 'tips', 'odds'])
            ->keyBy('type');

        foreach (PlanCategory::query()->orderBy('id')->get(['id', 'name', 'title']) as $cat) {
            $cid = $cat->id;
            if (! $cid) {
                continue;
            }
            $typeKey = 'v'.$cid;
            $pred = $vipPredictions->get($typeKey);
            $tips = $pred ? trim((string) ($pred->tips ?? '')) : '';
            $odds = $pred ? trim((string) ($pred->odds ?? '')) : '';
            // Enabled when a VIP tip exists for this plan.
            $data['v_'.$cid] = $pred ? $typeKey : '';
            $data['v_'.$cid.'_tips'] = $tips;
            $data['v_'.$cid.'_odds'] = $odds;
        }

        return response()->json([
            'ok' => true,
            'predictions' => $data,
        ]);
    }

    public function storeVip(Request $request, string $matchId): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();
        if (! is_array($payload)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        try {
            $existingVip = Prediction::query()
                ->where('match_id', $matchId)
                ->where('type', 'like', 'v%')
                ->pluck('type')
                ->all();

            $submittedVip = [];
            foreach (PlanCategory::query()->orderBy('id')->get() as $cat) {
                $cid = $cat->id;
                if (! $cid) {
                    continue;
                }

                $type = 'v'.$cid;
                $submittedVip[] = $type;

                $tips = trim((string) ($payload['v_'.$cid.'_tips'] ?? ''));
                $odds = trim((string) ($payload['v_'.$cid.'_odds'] ?? ''));
                // Legacy "Yes" enable select still accepted; tip/odds alone also enable.
                $enabledFlag = trim((string) ($payload['v_'.$cid] ?? ''));
                $hasContent = $tips !== '' || $odds !== '';

                if ($enabledFlag === '' && ! $hasContent) {
                    Prediction::query()
                        ->where('match_id', $matchId)
                        ->where('type', $type)
                        ->delete();

                    continue;
                }

                if ($tips === '' && $odds !== '') {
                    $tips = $odds;
                }

                if ($tips !== '' && $this->oddsBackfill->isMissingOdds($odds)) {
                    $odds = $this->oddsBackfill->resolveForTip($matchId, $tips);
                }

                Prediction::updateOrCreate(
                    ['match_id' => $matchId, 'type' => $type],
                    [
                        'match_id' => $matchId,
                        'type' => $type,
                        'tips' => $tips,
                        'odds' => $odds,
                        'vip_type' => (string) $cid,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            $toDelete = array_diff($existingVip, $submittedVip);
            if (! empty($toDelete)) {
                Prediction::query()
                    ->where('match_id', $matchId)
                    ->whereIn('type', $toDelete)
                    ->delete();
            }

            $this->oddsBackfill->fillMissingForMatch($matchId);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Save failed', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Persist Straight Win as home_win (1) or away_win (2), clearing the opposite side.
     */
    private function storeStraightWin(string $matchId, string $tips, string $odds): void
    {
        if ($tips === '' && $odds === '') {
            Prediction::query()
                ->where('match_id', $matchId)
                ->whereIn('type', ['home_win', 'away_win'])
                ->delete();

            return;
        }

        if ($tips === '' && $odds !== '') {
            $tips = $odds;
        }

        $canonical = strtolower(trim($tips));
        $isAway = in_array($canonical, ['2', 'away', 'away win', 'aw'], true);
        $target = $isAway ? 'away_win' : 'home_win';
        $other = $isAway ? 'home_win' : 'away_win';
        $normalizedTip = $isAway ? '2' : '1';

        if ($this->oddsBackfill->isMissingOdds($odds)) {
            $odds = $this->oddsBackfill->resolveForTip($matchId, $normalizedTip);
        }

        Prediction::query()
            ->where('match_id', $matchId)
            ->where('type', $other)
            ->delete();

        Prediction::updateOrCreate(
            ['match_id' => $matchId, 'type' => $target],
            [
                'match_id' => $matchId,
                'type' => $target,
                'tips' => $normalizedTip,
                'odds' => $odds,
                'vip_type' => 'regular',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
