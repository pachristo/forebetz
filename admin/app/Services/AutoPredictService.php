<?php

namespace App\Services;

use App\Helpers\ApiGateway;
use App\Models\APIodds;
use App\APIprediction;
use App\Models\Prediction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoPredictService
{
    public array $fixtures = [];
    public array $prediction = [];
    public array $last_home = [];
    public array $last_away = [];
    public string $formattedDate = '';

    /** Max predictions per market (type) for the date; null = unlimited. */
    public ?int $perTypeLimit = null;

    /** @var array<string, int> */
    protected array $typeCounts = [];

    public const PREDICTION_TYPES = [
        'cat_15' => '1_5_goal',
        'cat_25' => '2_5_goal',
        'cat_35' => '3_5_goal',
        'cat_2' => 'away_win',
        'cat_btts' => 'btts',
        'cat_dnd' => 'dnd',
        'cat_dc' => 'double_chance',
        'cat_x' => 'draw',
        'cat_1' => 'home_win',
        'cat_cs' => 'correct_score',
        'cat_weh' => 'weh',
        'cat_h1_5' => 'home_1_5_goals',
        'cat_a1_5' => 'away_1_5_goals',
    ];

    /** Markets with no bookmaker odds in api_odds; saved without odds instead of dropped. */
    public const NO_ODDS_MARKETS = ['weh', 'correct_score'];

    protected function loadTypeCounts(): void
    {
        $this->typeCounts = Prediction::query()
            ->join('fixtures', 'fixtures.match_id', '=', 'predictions.match_id')
            ->where('fixtures.match_date', $this->date)
            ->groupBy('predictions.type')
            ->selectRaw('predictions.type, count(*) as total')
            ->pluck('total', 'type')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    protected function typeIsFull(string $market): bool
    {
        return $this->perTypeLimit !== null && ($this->typeCounts[$market] ?? 0) >= $this->perTypeLimit;
    }

    protected function allTypesFull(): bool
    {
        if ($this->perTypeLimit === null) {
            return false;
        }

        foreach (self::PREDICTION_TYPES as $market) {
            if (! $this->typeIsFull($market)) {
                return false;
            }
        }

        return true;
    }

    /** @return array<string, int> */
    public function typeCounts(): array
    {
        return $this->typeCounts;
    }
    public function __construct(public string $date)
    {
    }

    public function getPredictions(): void
    {
        $date = $this->date ;

        $this->formattedDate = $date;

        $url = "fixtures?date=" . $date . '&timezone=Africa/Lagos';

        try {
            $response = ApiGateway::callAPI($url);

            if (!isset($response->body->response)) {
                \Log::warning("No fixtures response from API for date: {$date}");
                $this->fixtures = [];
                return;
            }

            if (count($response->body->response)) {
                $this->fixtures = $response->body->response;
                \Log::info("Fetched " . count($this->fixtures) . " fixtures for date: {$date}");
            } else {
                \Log::info("No fixtures found for date: {$date}");
                $this->fixtures = [];
            }
        } catch (\Exception $e) {
            \Log::error("Failed to fetch fixtures for date {$date}: " . $e->getMessage());
            $this->fixtures = [];
        }
    }
    public function makePrediction($final, $data)
    {
        // Initialize $data array
        $data = array_fill_keys(array_keys($data), '');

        // Helper function to safely get a value from an array
        $getValue = function ($array, $key, $default = 0) {
            return isset($array[$key]) ? $array[$key] : $default;
        };

        // 1x2 Predictions
        $winProbability = $getValue($final, '1x2', []);
        if ($getValue($winProbability, '1') >= 60) {
            $data['cat_1'] = 1;
        }

        if ($getValue($winProbability, '2') >= 60) {
            $data['cat_2'] = 2;
        }

        if (
            $getValue($winProbability, '1') >= 40 &&
            $getValue($winProbability, '2') >= 40 &&
            $getValue($final, '1.5', [])['+1.5'] > 70
        ) {
            $data['cat_x'] = 'x';
        }

        // Over/Under 1.5 Goals
        $overUnder1_5 = $getValue($final, '1.5', []);
        foreach ($overUnder1_5 as $key => $value) {
            if ($value > 80) {
                $data['cat_15'] = $key;
                break;
            }
        }

        // Over/Under 2.5 Goals
        $overUnder2_5 = $getValue($final, '2.5', []);
        foreach ($overUnder2_5 as $key => $value) {
            if ($value > 80) {
                $data['cat_25'] = $key;
                break;
            }
        }

        // Over/Under 3.5 Goals
        $overUnder3_5 = $getValue($final, '3.5', []);
        foreach ($overUnder3_5 as $key => $value) {
            if ($value > 80) {
                $data['cat_35'] = $key;
                break;
            }
        }

        // BTTS Predictions
        $btts = $getValue($final, 'btts', []);
        foreach ($btts as $key => $value) {
            if ($value > 70) {
                $data['cat_btts'] = $key;
                break;
            }
        }

        // Double Chance
        $doubleChance = $getValue($final, 'dc', []);
        foreach ($doubleChance as $key => $value) {
            if ($value > 75) {
                $data['cat_dc'] = $key;
                break;
            }
        }

        return $data;
    }
    public function calcProb($odds): float
    {
        return number_format((1 / ($odds == 0 ? 1 : $odds)) * 100, 2);
    }
    public function predictAction(): bool
    {
        //LEAST ODD 1.15************************************************************************
        $batchSize = 100;
        $processedCount = 0;
        $totalFixtures = count($this->fixtures);

        if ($this->perTypeLimit !== null) {
            $this->loadTypeCounts();
        }

        foreach ($this->fixtures as $key => $item) {
            if ($this->allTypesFull()) {
                \Log::info("All prediction types reached limit {$this->perTypeLimit} for {$this->date}");
                break;
            }

            if ($this->perTypeLimit !== null && Prediction::where('match_id', $item->fixture->id)->exists()) {
                continue;
            }

            try {
                $service = new FixtureService();



            $data = [
                'cat_cs' => '',
                'cat_1' => '',
                'cat_2' => '',
                'cat_x' => '',
                'cat_15' => '',
                'cat_25' => '',
                'cat_35' => '',
                'cat_dc' => '',
                'cat_btts' => '',
                'cat_weh' => '', // Win Either Half
                'cat_dnd' => '', // Draw No Bet
                'cat_h1_5' => '', // Home 1.5 Goals
                'cat_a1_5' => '', // Away 1.5 Goals
                // 'pred'=>[],
            ];
            $eligible = false;
            $time = $item->fixture->date != 'TBA' ? Carbon::parse($item->fixture->date)->format('H:i:s') : '00:00:00';
            //            if ($time < '12:00:00') continue;

            // get match odds
            $odds = APIodds::where('match_id', $item->fixture->id)->first();
            if ($odds == null) {
                \Log::warning("No odds found for fixture {$item->fixture->id}");
                continue;
            }

            $odds = $odds->odd_data;

            // get prediction with error handling
            try {
                $event = ApiGateway::callAPI('predictions?fixture=' . $item->fixture->id);
                if (!isset($event->body->response)) {
                    \Log::warning("No prediction response for fixture {$item->fixture->id}");
                    continue;
                }
                $this->prediction = $event->body->response;
            } catch (\Exception $e) {
                \Log::error("Failed to fetch predictions for fixture {$item->fixture->id}: " . $e->getMessage());
                continue;
            }

            //get last matches with error handling
            try {
                $last_home = ApiGateway::callAPI('fixtures?last=38&team=' . $item->teams->home->id . '&season=' . $item->league->season);
                if (!isset($last_home->body->response)) {
                    \Log::warning("No last home matches for team {$item->teams->home->id}");
                    $last_home = (object)['body' => (object)['response' => []]];
                }
                $last_home = $last_home->body->response;
            } catch (\Exception $e) {
                \Log::error("Failed to fetch last home matches for team {$item->teams->home->id}: " . $e->getMessage());
                $last_home = [];
            }

            try {
                $last_away = ApiGateway::callAPI('fixtures?last=38&team=' . $item->teams->away->id . '&season=' . $item->league->season);
                if (!isset($last_away->body->response)) {
                    \Log::warning("No last away matches for team {$item->teams->away->id}");
                    $last_away = (object)['body' => (object)['response' => []]];
                }
                $last_away = $last_away->body->response;
            } catch (\Exception $e) {
                \Log::error("Failed to fetch last away matches for team {$item->teams->away->id}: " . $e->getMessage());
                $last_away = [];
            }

            // \Log::debug($last_home);
            // \Log::debug($last_away);



            $prob_ab = $this->getOverallProbabilityWithCategories($item->teams->home->id, $last_home, $item->teams->away->id, $last_away, []);
            // $form=$this

            $data = $this->makePrediction($prob_ab, $data);
            $league_id = $item->league->id;

            // Prediction engine calls with error handling
            try {
                $tip = $service->predictionEngine($this->prediction, 'home_win');
                // $oddHome = $service->extractOdd($odds, 'home_win', $tip);
                $probHome = $this->calcProb($odds['Home'] ?? 1);

                if ($tip && $probHome >= 56 && $probHome <= 90) {
                    $eligible = true;
                    $data['cat_1'] = '1';
                    $data['cat_dc'] = '';
                }
            } catch (\Exception $e) {
                \Log::error("Error in home_win prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            // Probability between 60 and 80
            //check away win
            try {
                $tip = $service->predictionEngine($this->prediction, 'away_win');

                $probAway = $this->calcProb($odds['Away'] ?? 1);

                if ($tip && $probAway >= 56 && $probAway <= 90) {
                    $eligible = true;
                    $data['cat_2'] = '2';
                    $data['cat_dc'] = '';
                }
            } catch (\Exception $e) {
                \Log::error("Error in away_win prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            //check home win or draw
            try {
                $tip = $service->predictionEngine($this->prediction, 'double_chance');
                if ($tip && $data['cat_dc'] != '') {
                    $eligible = true;
                    $data['cat_dc'] = $tip;
                }
            } catch (\Exception $e) {
                \Log::error("Error in double_chance prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            if ($data['cat_1'] != '' && $data['cat_2'] != '') {
                $data['cat_1'] = '';
                $data['cat_2'] = '';
                $data['cat_dc'] = '12';

            }

            if ($data['cat_1'] != '' || $data['cat_2'] != '') {
                $data['cat_dc'] = '';
            }


            if ($data['cat_dc'] != '') {
                if (isset($odds[$data['cat_dc']])) {
                    if ($odds[$data['cat_dc']] < 1.2) {
                        $data['cat_dc'] = '';
                    }

                }

            }


            //            //check away win or draw

            if ($data['cat_15'] != '') {
                if (isset($odds[$data['cat_15']])) {
                    if ($odds[$data['cat_15']] > 1.9 || $odds[$data['cat_15']] < 1.3) {
                        $data['cat_15'] = '';
                    }

                }

            }

            if ($data['cat_25'] != '') {
                if (isset($odds[$data['cat_25']])) {
                    if ($odds[$data['cat_25']] > 1.9 || $odds[$data['cat_25']] < 1.4) {
                        $data['cat_25'] = '';
                    }

                }

            }



            if ($data['cat_35'] != '') {
                if (isset($odds[$data['cat_35']])) {
                    if ($odds[$data['cat_35']] > 1.9 || $odds[$data['cat_35']] < 1.4) {
                        $data['cat_35'] = '';
                    }
                }
            }

            //check BTTS
            try {
                $tip = $service->predictionEngine($this->prediction, 'both_teams_score');
                if ($tip && $data['cat_btts'] != '') {
                    $eligible = true;
                    $data['cat_btts'] = $tip;
                }
            } catch (\Exception $e) {
                \Log::error("Error in BTTS prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            //check Win Either Half (WEH) - Based on Performance
            try {
                $wehAnalysis = $this->analyzeWinEitherHalf($last_home, $last_away, $item->teams->home->id, $item->teams->away->id);
                if ($wehAnalysis['tip']) {
                    $eligible = true;
                    if ($wehAnalysis['tip'] === 'home') {
                        $data['cat_weh'] = 'HWEH'; // Home Win Either Half
                    } elseif ($wehAnalysis['tip'] === 'away') {
                        $data['cat_weh'] = 'AWEH'; // Away Win Either Half
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error in WEH prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            //check Draw No Bet (DNB) - Based on Performance
            try {
                $dnbAnalysis = $this->analyzeDrawNoBet($last_home, $last_away, $item->teams->home->id, $item->teams->away->id);
                if ($dnbAnalysis['tip']) {
                    $eligible = true;
                    if ($dnbAnalysis['tip'] === 'home') {
                        $data['cat_dnd'] = '1 DNB'; // Home Draw No Bet
                    } elseif ($dnbAnalysis['tip'] === 'away') {
                        $data['cat_dnd'] = '2 DNB'; // Away Draw No Bet
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error in DNB prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            // Home Team Over 1.5 Goals (cat_h1_5)
            try {
                $homeGoalsAnalysis = $this->analyzeTeamGoals1_5($last_home, $item->teams->home->id, 'home');
                if ($homeGoalsAnalysis['tip']) {
                    $eligible = true;
                    $data['cat_h1_5'] = 'HTo1.5';
                }
            } catch (\Exception $e) {
                \Log::error("Error in Home 1.5 Goals prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            // Away Team Over 1.5 Goals (cat_a1_5)
            try {
                $awayGoalsAnalysis = $this->analyzeTeamGoals1_5($last_away, $item->teams->away->id, 'away');
                if ($awayGoalsAnalysis['tip']) {
                    $eligible = true;
                    $data['cat_a1_5'] = 'ATo1.5';
                }
            } catch (\Exception $e) {
                \Log::error("Error in Away 1.5 Goals prediction for fixture {$item->fixture->id}: " . $e->getMessage());
            }

            if ($data['cat_1'] == '' && $data['cat_2'] == '' && $data['cat_35'] == '' && $data['cat_25'] == '' && $data['cat_15'] == '' && $data['cat_dc'] == '' && $data['cat_btts'] == '' && $data['cat_weh'] == '' && $data['cat_dnd'] == '' && $data['cat_h1_5'] == '' && $data['cat_a1_5'] == '')
                continue;
            //            //check under 2.5 goals
////            if((int)$prediction->prob_U >= 70 && $this->oddChecker($odds['u+2.5'] ?? 0)) {
////                $eligible = true;
////                $this->savePrediction('2_5_goals', 'Under 2.5', $prediction->prob_U, $key, $odds['u+2.5'] ?? null);
////            }
//

            // Assuming $prob_ab['cs'] contains the correct score predictions
// and the required categories are already present in $data

            if (!empty($prob_ab['cs'])) {
                // Prioritize based on categories in $data
                foreach ($prob_ab['cs'] as $score => $probability) {
                    [$homeGoals, $awayGoals] = explode('-', $score); // Split the correct score
                    [$homeGoals, $awayGoals] = [intval($homeGoals), intval($awayGoals)];
                    $totalGoals = $homeGoals + $awayGoals;

                    // Check for Home Win (cat_1)
                    if ($data['cat_1'] == 1 && $homeGoals > $awayGoals) {
                        $data['cat_cs'] = $score;
                        break;
                    }

                    // Check for Away Win (cat_2)
                    if ($data['cat_2'] == 2 && $awayGoals > $homeGoals) {
                        $data['cat_cs'] = $score;
                        break;
                    }

                    // Check for Double Chance (cat_dc)
                    if (
                        in_array($data['cat_dc'], ['1x', 'x2', '12'], true) &&
                        (
                            ($data['cat_dc'] === '1x' && $homeGoals >= $awayGoals) || // Home win or draw
                            ($data['cat_dc'] === 'x2' && $awayGoals >= $homeGoals) || // Away win or draw
                            ($data['cat_dc'] === '12' && $homeGoals != $awayGoals)   // Either side wins
                        )
                    ) {
                        $data['cat_cs'] = $score;
                        break;
                    }

                    // Check for BTTS (cat_btts)
                    if (
                        ($data['cat_btts'] === 'yes' && $homeGoals > 0 && $awayGoals > 0) || // GG
                        ($data['cat_btts'] === 'no' && ($homeGoals == 0 || $awayGoals == 0)) // NG
                    ) {
                        $data['cat_cs'] = $score;
                        break;
                    }

                    // Check for Over/Under categories (cat_15, cat_25, cat_35)
                    if (
                        ($data['cat_15'] === '+' && $totalGoals > 1.5) || // Over 1.5 goals
                        ($data['cat_15'] === '-' && $totalGoals <= 1.5) || // Under 1.5 goals
                        ($data['cat_25'] === '+' && $totalGoals > 2.5) || // Over 2.5 goals
                        ($data['cat_25'] === '-' && $totalGoals <= 2.5) || // Under 2.5 goals
                        ($data['cat_35'] === '+' && $totalGoals > 3.5) || // Over 3.5 goals
                        ($data['cat_35'] === '-' && $totalGoals <= 3.5)    // Under 3.5 goals
                    ) {
                        $data['cat_cs'] = $score;
                        break;
                    }

                    // Check for Win Either Half (WEH)
                    if (
                        ($data['cat_weh'] === 'HWEH' && $homeGoals > $awayGoals) || // Home wins (likely to win either half)
                        ($data['cat_weh'] === 'AWEH' && $awayGoals > $homeGoals)    // Away wins (likely to win either half)
                    ) {
                        $data['cat_cs'] = $score;
                        break;
                    }

                    // Check for Draw No Bet (DNB)
                    if (
                        ($data['cat_dnd'] === '1 DNB' && $homeGoals > $awayGoals) || // Home wins (draw refunded)
                        ($data['cat_dnd'] === '2 DNB' && $awayGoals > $homeGoals)    // Away wins (draw refunded)
                    ) {
                        $data['cat_cs'] = $score;
                        break;
                    }
                }

                // Default to the first key if no specific condition matches
                if (!isset($data['cat_cs'])) {
                    $data['cat_cs'] = array_key_first($prob_ab['cs']);
                }
            } else {
                $data['cat_cs'] = null; // No correct score predictions available
            }

            if ($eligible) {
                try {
                    $this->saveFixtureAction($key, $odds, $data, $this->prediction, $prob_ab, $league_id);
                } catch (\Exception $e) {
                    \Log::error("Error saving fixture action for {$item->fixture->id}: " . $e->getMessage());
                }
            }

            } catch (\Exception $e) {
                // Log the error but continue processing other fixtures
                \Log::error("Error processing fixture {$item->fixture->id}: " . $e->getMessage());
                continue;
            }

            // Increment processed count
            $processedCount++;

            // Sleep for 1 minute after every 100 fixtures (and not on the last fixture)
            if ($processedCount % $batchSize === 0 && $processedCount < $totalFixtures) {
                \Log::info("Processed {$processedCount}/{$totalFixtures} fixtures. Sleeping for 1 minute...");
                sleep(60); // Sleep for 1 minute (60 seconds)
            }
        }

        $this->selectFreePicks();
        $this->selectSoloPick();
        $this->selectBankerTips();

        //        info('Predictions Updated');
        return true;
    }

    public function getodds($tip, $odds)
    {
        if (is_array($odds) && isset($odds[$tip]) && (float) $odds[$tip] > 1) {
            return $odds[$tip];
        }

        if (is_array($odds) && preg_match('/^([12]) dnb$/i', (string) $tip, $m)) {
            $win = (float) ($odds[$m[1] === '1' ? 'Home' : 'Away'] ?? 0);
            $draw = (float) ($odds['Draw'] ?? 0);

            return ($win > 1 && $draw > 1) ? round($win * ($draw - 1) / $draw, 2) : '';
        }

        $tip = strtolower($tip);
        $options = [
            '1' => 'Home',
            '2' => 'Away',
            '1x' => '1x',
            '12' => '12',
            'x2' => 'x2',
            '+1.5' => '+1.5',
            '-1.5' => '-1.5',
            '+2.5' => '+2.5',
            '-2.5' => '-2.5',
            '+3.5' => '+3.5',

            '-3.5' => '-3.5',
            'btts' => 'btts_yes',
            'btts_no' => 'btts_no',
            'yes' => 'btts_yes',
            'no' => 'btts_no',
            'X' => 'Draw',

            'x' => 'Draw',

            '1X' => '1X',
            'X2' => 'X2',
            // '12' => '1X2',
            'fh-1.5' => 'fh-1.5',
            'fh+1.5' => 'fh+1.5',
            'fh-2.5' => 'fh-2.5',
            'fh+2.5' => 'fh+2.5',
            'fh-0.5' => '-0.5',
            'fh+0.5' => '+0.5',
            'ht-0.5' => '-0.5',
            'ht+0.5' => '+0.5',
            'ht-1.5' => 'fh-1.5',
            'ht+1.5' => 'fh+1.5',
            'ht-2.5' => 'fh-2.5',
            'ht+2.5' => 'fh+2.5',


            // '12' => '1X2',
            'HTu0.5' => 'HTu0.5',
            'HTo0.5' => 'HTo0.5',
            'HTu1.5' => 'HTu1.5',
            'HTo1.5' => 'HTo1.5',
            'HTu2.5' => 'HTu2.5',
            'HTo2.5' => 'HTo2.5',

            'ATu0.5' => 'ATu0.5',
            'ATo0.5' => 'ATo0.5',
            'ATu1.5' => 'ATu1.5',
            'ATo1.5' => 'ATo1.5',
            'ATu2.5' => 'ATu2.5',
            'ATo2.5' => 'ATo2.5',


            '-4.5' => '-4.5',
            '1/1' => 'Home/Home',
            '2/2' => 'Away/Away',
            'X/1' => 'Draw/Home',
            '1/2' => 'Home/Away',
            '2/1' => 'Away/Home',
            'X/2' => 'Draw/Away',
            'X/X' => 'Draw/Draw',
            '1/X' => 'Home/Draw',
            '2/X' => 'Away/Draw',
            'hweh' => 'hweh',
            'aweh' => 'aweh',
            'dnd1' => 'dnd1',
            'dnd2' => 'dnd2',
            'ht1' => 'ht1',
            'ht2' => 'ht2',
            'htx' => 'htx',

        ];

        // Check if tip exists in options and if odds is an array
        if (!isset($options[$tip]) || !is_array($odds)) {
            return '';
        }

        $oddsKey = $options[$tip];
        return $odds[$oddsKey] ?? '';

    }

    protected function getmatchdds($mat)
    {
        $mat = $this->getBetsByBookmakerName($mat);
        $odds = (new DailyOddsService())->defaultOddsMap();
        $final = [];
        if (!isset($mat['bets'])) {
            return [];
        }
        // return $mat;
        foreach ($mat['bets'] as $e => $v) {
            // return $v;
            if ($v['id'] == 1) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');

                $result = array_combine($keys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 5) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Over ') !== false) {
                        return str_replace('Over ', '+', $key);
                    } else {
                        return str_replace('Under ', '-', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }

            // Bet id 6 — Goals Over/Under First Half
            if ($v['id'] == 6) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(
                    static fn (string $key): string => DailyOddsService::mapFirstHalfOverUnderKey($key),
                    $keys
                );
                $result = array_combine($modifiedKeys, $values);
                if ($result !== false && $result !== []) {
                    $final = array_merge($final, $result);
                }
            }

            if ($v['id'] == 16) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Over ') !== false) {
                        return str_replace('Over ', 'HTo', $key);
                    } else {
                        return str_replace('Under ', 'HTu', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 17) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Over ') !== false) {
                        return str_replace('Over ', 'ATo', $key);
                    } else {
                        return str_replace('Under ', 'ATu', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 8) {
                $result = [
                    'btts_yes' => $v['values'][0]['odd'] ?? 1,
                    'btts_no' => $v['values'][1]['odd'] ?? 1,
                ];
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }

            if ($v['id'] == 182) {
                $result = [
                    'dnb1' => $v['values'][0]['odd'] ?? 1,
                    'dnb2' => $v['values'][1]['odd'] ?? 1,
                ];
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 13) {
                $result = [
                    'ht1' => $v['values'][0]['odd'] ?? 1,
                    'htx' => $v['values'][1]['odd'] ?? 1,
                    'ht2' => $v['values'][2]['odd'] ?? 1,
                ];
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == DailyOddsService::BET_ID_TO_WIN_EITHER_HALF) {
                $result = DailyOddsService::parseToWinEitherHalfOdds($v);
                if ($result !== []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 12) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Home/Draw') !== false) {
                        return str_replace('Home/Draw', '1x', $key);
                    } elseif (strpos($key, 'Home/Away') !== false) {
                        return str_replace('Home/Away', '1x2', $key);
                    } else {
                        return str_replace('Draw/Away', 'x2', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }


            if ($v['id'] == 7) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');

                $result = array_combine($keys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }


        }

        return array_merge($odds, $final);
    }
    protected function getRandomNonEmptyValue($array)
    {
        $nonEmptyValues = array_filter($array); // Filter out empty values
        return !empty($nonEmptyValues)
            ? $nonEmptyValues[array_rand($nonEmptyValues)]
            : null; // Get random value or null if array is empty
    }

    public function selectFreePicks(): void
    {

    }

    public function selectSoloPick(): void
    {
        // if(!Prediction::whereMatchDate($this->formattedDate)->whereMarket('solo_pick')->exists()) {
        //     $tip = Prediction::whereMatchDate($this->formattedDate)
        //         ->whereBetween('odd', [1.50, 1.80])
        //         ->where('probability', '>=', 60)
        //         ->whereIn('market', ['2_5_goals', 'home_win', 'away_win', 'both_teams_score'])
        //         ->inRandomOrder()
        //         ->first();

        //     if ($tip)
        //     {
        //         $tip->token = GeneralService::generateToken('predictions');
        //         $tip->market = 'solo_pick';
        //         Prediction::create($tip->toArray());
        //     }
        // }
    }

    public function selectBankerTips(): void
    {
        // if(!Prediction::whereMatchDate($this->formattedDate)->whereMarket('banker_tips')->exists()) {
        //     $tips = Prediction::whereMatchDate($this->formattedDate)
        //         ->whereBetween('odd', [1.25, 1.40])
        //         ->where('probability', '>=', 70)
        //         ->whereIn('market', ['home_win', 'away_win'])
        //         ->groupBy('fixture_id')
        //         ->take(2)
        //         ->inRandomOrder()
        //         ->get();

        //     if ($tips)
        //     {
        //         foreach ($tips as $tip) {
        //             $tip->token = GeneralService::generateToken('predictions');
        //             $tip->market = 'banker_tips';
        //             Prediction::create($tip->toArray());
        //         }
        //     }
        // }
    }


    private function savePrediction($market, $tip, $prob, $index, $odds, $data): void
    {
        $item = $this->fixtures[$index];


        $service = new FixtureService();
        $odd = $service->extractOdd($odds, $market, $tip);
        $prob = $market == 'draws' ? rand(60, 70) : $service->calcProb($odd);

        if ($odd && $odd > 1.15) {
            // APIcategory::updateOrCreate(['match_id', $item->fixture->id], $data);

        }
    }

    private function saveFixtureAction($index, $odds, $data, $pred, $prob_ab, $league_id): void
    {
        $item = $this->fixtures[$index];


        $service = new FixtureService();


        $data['league_id'] = $league_id;

        $data['match_id'] = $item->fixture->id;

        foreach (self::PREDICTION_TYPES as $key => $market) {
            if (($data[$key] ?? '') != '') {
                if ($this->typeIsFull($market)) {
                    continue;
                }

                $tip = $data[$key];
                $odds_new= (float) $this->getodds($tip, $odds);
                $withoutOdds = in_array($market, self::NO_ODDS_MARKETS, true) && $odds_new <= 1;

                if (! $withoutOdds && $odds_new < 1.15) continue;

                $this->typeCounts[$market] = ($this->typeCounts[$market] ?? 0) + 1;
               // save prediction
                Prediction::updateOrCreate(
                    ['match_id' => $item->fixture->id, 'type' => $market],
                    [
                        'match_id' => $item->fixture->id,
                        'type' => $market,
                        'tips' => $tip,
                        'odds' => $withoutOdds ? null : $odds_new,
                        'prob' => $withoutOdds ? null : number_format((1 / ($odds_new == 0 ? 1 : $odds_new)) * 100, 2),
                        'vip_type' => 'regular',
                    ]
                );
            }
        }


        // APIcategory::updateOrCreate(['match_id' => $item->fixture->id], $data);

        $data = [];

        // simple approach: delete existing predictions for fixture then recreate non-empty
        // Prediction::where('match_id', operator: $this->fixtureId)->delete();

        // foreach ($data as $type => $tips) {
        //     if ($tips === null || $tips === '') continue;
        //     Prediction::create([
        //         'match_id' => $this->fixtureId,
        //         'type' => $type,
        //         'tips' => $tips,
        //         'vip_type' => 'regular',
        //     ]);
        // }

        APIprediction::updateOrCreate(['match_id' => $item->fixture->id], [
            'match_id' => $item->fixture->id,

            'pred_data' => $pred,
            'h2h_pred' => $prob_ab,
            'home_form' => $prob_ab['home_form'] ?? '',
            'away_form' => $prob_ab['away_form'] ?? '',

        ]);
    }
    public function calculateCorrectScoreProbability($matches)
    {
        $scoreCounts = [];
        $totalMatches = count($matches);

        foreach ($matches as $e => $match) {
            $homeGoals = $match->goals->home;
            $awayGoals = $match->goals->away;

            $score = "{$homeGoals}-{$awayGoals}";

            if (!isset($scoreCounts[$score])) {
                $scoreCounts[$score] = 0;
            }
            $scoreCounts[$score]++;

            if ($e == 5) {
                $totalMatches = 6;
                break;
            }
        }

        // Calculate probabilities for each score
        $probabilities = [];
        foreach ($scoreCounts as $score => $count) {
            $probabilities[$score] = round(($count / $totalMatches) * 100, 2);
        }

        return $probabilities;
    }

    public function getBetsByBookmakerName($data): array
    {
        if (!isset($data[0]))
            return [];
        // \Log::debug($data[0]->bookmakers );
        $bookmakers = $data[0]->bookmakers ?? [];
        $bookmakersArray = json_decode(json_encode($bookmakers), true);
        // \Log::debug($bookmakersArray);
        return $bookmakersArray[0] ?? []; // Return an empty array if the bookmaker is not found
    }
    public function getTeamProbabilities($teamId, $matches)
{
    $totalMatches = count($matches);
    $wins = 0;
    $draws = 0;
    $losses = 0;
    $form_str = '';
    $homeHalftimeWins = 0;
    $awayHalftimeWins = 0;
    $totalHalftimeMatches = 0;

    foreach ($matches as $e => $match) {
        $homeId = $match->teams->home->id;
        $awayId = $match->teams->away->id;

        // Use $match->score structure
        $score = $match->score;
        $fulltime = $score->fulltime ?? null;
        $halftime = $score->halftime ?? null;

        // Final score for W/D/L
        $homeGoals = $fulltime->home ?? 0;
        $awayGoals = $fulltime->away ?? 0;

        // Halftime score for halftime win %
        $homeHtGoals = $halftime->home ?? 0;
        $awayHtGoals = $halftime->away ?? 0;

        if ($homeId == $teamId) {
            // Fulltime result
            if ($homeGoals > $awayGoals) {
                $wins++;
                $form_str .= 'W';
            } elseif ($homeGoals < $awayGoals) {
                $losses++;
                $form_str .= 'L';
            } else {
                $draws++;
                $form_str .= 'D';
            }

            // Halftime win (home team)
            if ($homeHtGoals > $awayHtGoals) {
                $homeHalftimeWins++;
            }
            $totalHalftimeMatches++;

        } elseif ($awayId == $teamId) {
            // Fulltime result
            if ($awayGoals > $homeGoals) {
                $wins++;
                $form_str .= 'W';
            } elseif ($awayGoals < $homeGoals) {
                $losses++;
                $form_str .= 'L';
            } else {
                $draws++;
                $form_str .= 'D';
            }

            // Halftime win (away team)
            if ($awayHtGoals > $homeHtGoals) {
                $awayHalftimeWins++;
            }
            $totalHalftimeMatches++;
        }

        if ($e == 4) { // Fixed: Last 5 matches (0,1,2,3,4)
            $totalMatches = 5;
            break;
        }
    }

    // Calculate halftime win percentage
    $halftimeWinPercentage = $totalHalftimeMatches > 0
        ? (($homeHalftimeWins + $awayHalftimeWins) / $totalHalftimeMatches * 100)
        : 0;

    if ($totalMatches > 0) {
        return [
            'win' => $wins / $totalMatches,
            'draw' => $draws / $totalMatches,
            'loss' => $losses / $totalMatches,
            'form' => substr($form_str, -5), // Last 5 matches
            'halftime_win_pct' => round($halftimeWinPercentage, 1) // NEW: Halftime win %
        ];
    }

    return [
        'win' => 0,
        'draw' => 0,
        'loss' => 0,
        'form' => '',
        'halftime_win_pct' => 0
    ];
}
    private function oddChecker($odd): bool
    {
        $odd = (float) $odd;
        if ($odd >= 1.11 && $odd <= 1.70)
            return true;
        return false;
    }

    public function calculateOverUnderProbability($matches, $threshold)
    {
        $totalMatches = count($matches);
        $over = 0;
        $under = 0;

        foreach ($matches as $e => $match) {
            $totalGoals = $match->goals->home + $match->goals->away;
            if ($totalGoals > $threshold) {
                $over++;
            } else {
                $under++;
            }
            if ($e == 5) {
                $totalMatches = 6;
                break;
            }
        }

        if ($totalMatches > 0) {
            return [
                'over' => $over / $totalMatches,
                'under' => $under / $totalMatches,
            ];
        }

        return ['over' => 0, 'under' => 0];
    }

    public function calculateBTTSProbability($matches)
    {
        $totalMatches = count($matches);
        $btts = 0;
        $noBtts = 0;

        foreach ($matches as $e => $match) {
            $homeGoals = $match->goals->home;
            $awayGoals = $match->goals->away;

            if ($homeGoals > 0 && $awayGoals > 0) {
                $btts++;
            } else {
                $noBtts++;
            }
            if ($e == 5) {
                $totalMatches = 6;
                break;
            }
        }

        if ($totalMatches > 0) {
            return [
                'yes' => $btts / $totalMatches,
                'no' => $noBtts / $totalMatches,
            ];
        }

        return ['yes' => 0, 'no' => 0];
    }

    public function calculateDoubleChanceProbability($teamId, $matches)
    {
        $probabilities = $this->getTeamProbabilities($teamId, $matches);

        // Double chance probabilities:
        // 1X = Win or Draw
        // X2 = Draw or Loss
        // 12 = Win or Loss
        return [
            '1X' => $probabilities['win'] + $probabilities['draw'],
            'X2' => $probabilities['draw'] + $probabilities['loss'],
            '12' => $probabilities['win'] + $probabilities['loss'],
        ];
    }

    public function getOverallProbabilityWithCategories($homeTeamId, $lastHomeMatches, $awayTeamId, $lastAwayMatches, $data = [])
    {
        if (empty($lastHomeMatches)) {
            $lastHomeMatches = [];
        }
        if (empty($lastAwayMatches)) {
            $lastAwayMatches = [];
        }

        $homeProbabilities = $this->getTeamProbabilities($homeTeamId, $lastHomeMatches);
        $awayProbabilities = $this->getTeamProbabilities($awayTeamId, $lastAwayMatches);

        $overallWin = ($homeProbabilities['win'] + $awayProbabilities['loss']) / 2;
        $overallDraw = ($homeProbabilities['draw'] + $awayProbabilities['draw']) / 2;
        $overallLoss = ($homeProbabilities['loss'] + $awayProbabilities['win']) / 2;

        $probabilities = [
            '1' => round($overallWin * 100, 0),
            'X' => round($overallDraw * 100, 0),
            '2' => round($overallLoss * 100, 0),
        ];

        // Handle odds fallback
        if ($probabilities['1'] == 0 && $probabilities['X'] == 0 && $probabilities['2'] == 0) {
            $homeOdds = $data['Home'] ?? 1;
            $awayOdds = $data['Away'] ?? 1;
            $drawOdds = $data['Draw'] ?? 1;

            $homeProb = 1 / $homeOdds;
            $awayProb = 1 / $awayOdds;
            $drawProb = 1 / $drawOdds;

            $totalProb = $homeProb + $awayProb + $drawProb;

            $probabilities['1'] = round(($homeProb / $totalProb) * 100, 0);
            $probabilities['X'] = round(($drawProb / $totalProb) * 100, 0);
            $probabilities['2'] = round(($awayProb / $totalProb) * 100, 0);
        }

        // Over/Under probabilities
        $overUnder1_5 = $this->calculateOverUnderProbability(array_merge($lastHomeMatches, $lastAwayMatches), 1.5);
        $overUnder2_5 = $this->calculateOverUnderProbability(array_merge($lastHomeMatches, $lastAwayMatches), 2.5);
        $overUnder3_5 = $this->calculateOverUnderProbability(array_merge($lastHomeMatches, $lastAwayMatches), 3.5);

        // Both Teams to Score (BTTS) probabilities
        $btts = $this->calculateBTTSProbability(array_merge($lastHomeMatches, $lastAwayMatches));

        // Double Chance probabilities
        $homeDoubleChance = $this->calculateDoubleChanceProbability($homeTeamId, $lastHomeMatches);
        $awayDoubleChance = $this->calculateDoubleChanceProbability($awayTeamId, $lastAwayMatches);
        $doubleChance = [
            '1X' => round($homeDoubleChance['1X'] * 100, 0),
            'X2' => round($awayDoubleChance['X2'] * 100, 0),
            '12' => round(($homeDoubleChance['12'] + $awayDoubleChance['12']) / 2 * 100, 0),
        ];
        // Correct Score probabilities
        $correctScores = $this->calculateCorrectScoreProbability(array_merge($lastHomeMatches, $lastAwayMatches));

        $final = [
            '1x2' => $probabilities,
            '1.5' => [
                '+1.5' => round($overUnder1_5['over'] * 100, 0),
                '-1.5' => round($overUnder1_5['under'] * 100, 0),
            ],
            '2.5' => [
                '+2.5' => round($overUnder2_5['over'] * 100, 0),
                '-2.5' => round($overUnder2_5['under'] * 100, 0),
            ],
            '3.5' => [
                '+3.5' => round($overUnder3_5['over'] * 100, 0),
                '-3.5' => round($overUnder3_5['under'] * 100, 0),
            ],
            'btts' => [
                'yes' => round($btts['yes'] * 100, 0),
                'no' => round($btts['no'] * 100, 0),
            ],
            'dc' => $doubleChance,
            'cs' => $correctScores,
            'home_form' => $homeProbabilities['form'],
            'away_form' => $awayProbabilities['form'],

        ];

        return $final;
    }

    /**
     * Analyze Win Either Half based on team performance in previous games
     *
     * Performance-based analysis criteria:
     * - Analyzes last 10 games for each team
     * - Counts first half wins and second half wins separately
     * - Calculates "either half win rate" (team wins at least one half)
     * - Minimum 50% either half win rate required
     * - Requires significant performance gap between teams
     *
     * A team wins either half if they consistently win first or second halves
     */
    private function analyzeWinEitherHalf($homeMatches, $awayMatches, $homeTeamId, $awayTeamId): array
    {
        $result = ['tip' => null, 'confidence' => 0];

        try {
            // Analyze home team's performance
            $homeStats = $this->calculateHalfTimePerformance($homeMatches, $homeTeamId);

            // Analyze away team's performance
            $awayStats = $this->calculateHalfTimePerformance($awayMatches, $awayTeamId);

            // Home team analysis for WEH
            $homeWehScore = 0;
            if ($homeStats['first_half_wins'] >= 3 || $homeStats['second_half_wins'] >= 3) {
                $homeWehScore += ($homeStats['first_half_wins'] + $homeStats['second_half_wins']) * 10;
            }
            if ($homeStats['either_half_win_rate'] >= 0.6) {
                $homeWehScore += 30;
            }
            if ($homeStats['total_games'] >= 5 && $homeStats['either_half_win_rate'] >= 0.5) {
                $homeWehScore += 20;
            }

            // Away team analysis for WEH
            $awayWehScore = 0;
            if ($awayStats['first_half_wins'] >= 3 || $awayStats['second_half_wins'] >= 3) {
                $awayWehScore += ($awayStats['first_half_wins'] + $awayStats['second_half_wins']) * 10;
            }
            if ($awayStats['either_half_win_rate'] >= 0.6) {
                $awayWehScore += 30;
            }
            if ($awayStats['total_games'] >= 5 && $awayStats['either_half_win_rate'] >= 0.5) {
                $awayWehScore += 20;
            }

            // Determine tip based on performance
            if ($homeWehScore >= 50 && $homeWehScore > $awayWehScore + 20) {
                $result['tip'] = 'home';
                $result['confidence'] = min($homeWehScore, 100);
            } elseif ($awayWehScore >= 50 && $awayWehScore > $homeWehScore + 20) {
                $result['tip'] = 'away';
                $result['confidence'] = min($awayWehScore, 100);
            }

        } catch (\Exception $e) {
            \Log::error("Error analyzing WEH performance: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Analyze Draw No Bet based on team performance in previous games
     *
     * Performance-based analysis criteria:
     * - Analyzes last 8 games for overall performance
     * - Analyzes last 5 games for recent form
     * - Requires 50%+ win rate with 20-40% draw rate (balanced risk)
     * - Minimum 60% confidence score required
     * - Recent form: at least 3 wins in last 5 games
     * - Loss rate must be under 30%
     *
     * Suitable when a team has strong win record but occasional draws
     */
    private function analyzeDrawNoBet($homeMatches, $awayMatches, $homeTeamId, $awayTeamId): array
    {
        $result = ['tip' => null, 'confidence' => 0];

        try {
            // Analyze home team's performance
            $homeStats = $this->calculateTeamPerformance($homeMatches, $homeTeamId);

            // Analyze away team's performance
            $awayStats = $this->calculateTeamPerformance($awayMatches, $awayTeamId);

            // Home team DNB analysis
            $homeDnbScore = 0;
            if ($homeStats['win_rate'] >= 0.5 && $homeStats['draw_rate'] >= 0.2 && $homeStats['draw_rate'] <= 0.4) {
                $homeDnbScore += 40;
            }
            if ($homeStats['win_rate'] >= 0.6) {
                $homeDnbScore += 30;
            }
            if ($homeStats['loss_rate'] < 0.3) {
                $homeDnbScore += 20;
            }
            if ($homeStats['recent_form_wins'] >= 3 && $homeStats['total_games'] >= 6) {
                $homeDnbScore += 25;
            }

            // Away team DNB analysis
            $awayDnbScore = 0;
            if ($awayStats['win_rate'] >= 0.5 && $awayStats['draw_rate'] >= 0.2 && $awayStats['draw_rate'] <= 0.4) {
                $awayDnbScore += 40;
            }
            if ($awayStats['win_rate'] >= 0.6) {
                $awayDnbScore += 30;
            }
            if ($awayStats['loss_rate'] < 0.3) {
                $awayDnbScore += 20;
            }
            if ($awayStats['recent_form_wins'] >= 3 && $awayStats['total_games'] >= 6) {
                $awayDnbScore += 25;
            }

            // Determine tip based on performance
            if ($homeDnbScore >= 60 && $homeDnbScore > $awayDnbScore + 15) {
                $result['tip'] = 'home';
                $result['confidence'] = min($homeDnbScore, 100);
            } elseif ($awayDnbScore >= 60 && $awayDnbScore > $homeDnbScore + 15) {
                $result['tip'] = 'away';
                $result['confidence'] = min($awayDnbScore, 100);
            }

        } catch (\Exception $e) {
            \Log::error("Error analyzing DNB performance: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Calculate halftime performance statistics for WEH analysis
     */
    private function calculateHalfTimePerformance($matches, $teamId): array
    {
        $stats = [
            'total_games' => 0,
            'first_half_wins' => 0,
            'second_half_wins' => 0,
            'either_half_wins' => 0,
            'either_half_win_rate' => 0
        ];

        if (empty($matches) || count($matches) < 3) {
            return $stats;
        }

        // Take last 10 games for analysis
        $recentMatches = array_slice($matches, -10);
        $stats['total_games'] = count($recentMatches);

        foreach ($recentMatches as $match) {
            if (!isset($match->score->halftime) || !isset($match->score->fulltime)) {
                continue;
            }

            $htHome = $match->score->halftime->home ?? 0;
            $htAway = $match->score->halftime->away ?? 0;
            $ftHome = $match->score->fulltime->home ?? 0;
            $ftAway = $match->score->fulltime->away ?? 0;

            // Calculate second half scores
            $secondHalfHome = $ftHome - $htHome;
            $secondHalfAway = $ftAway - $htAway;

            $isHome = ($match->teams->home->id == $teamId);

            // Check first half performance
            if ($isHome && $htHome > $htAway) {
                $stats['first_half_wins']++;
            } elseif (!$isHome && $htAway > $htHome) {
                $stats['first_half_wins']++;
            }

            // Check second half performance
            if ($isHome && $secondHalfHome > $secondHalfAway) {
                $stats['second_half_wins']++;
            } elseif (!$isHome && $secondHalfAway > $secondHalfHome) {
                $stats['second_half_wins']++;
            }

            // Check if team won either half
            $wonFirstHalf = ($isHome && $htHome > $htAway) || (!$isHome && $htAway > $htHome);
            $wonSecondHalf = ($isHome && $secondHalfHome > $secondHalfAway) || (!$isHome && $secondHalfAway > $secondHalfHome);

            if ($wonFirstHalf || $wonSecondHalf) {
                $stats['either_half_wins']++;
            }
        }

        $stats['either_half_win_rate'] = $stats['total_games'] > 0 ? $stats['either_half_wins'] / $stats['total_games'] : 0;

        return $stats;
    }

    /**
     * Analyze whether a team consistently scores over 1.5 goals (2+ goals) per game.
     *
     * Criteria (last 8 matches):
     * - Team scored 2+ goals in at least 65% of recent games
     * - Minimum 5 matches required
     * - Odds filter applied downstream (must be >= 1.15)
     */
    private function analyzeTeamGoals1_5(array $matches, int $teamId, string $side): array
    {
        $result = ['tip' => null, 'confidence' => 0];

        if (empty($matches) || count($matches) < 5) {
            return $result;
        }

        $recentMatches = array_slice($matches, -8);
        $total = count($recentMatches);
        $over = 0;

        foreach ($recentMatches as $match) {
            if (!isset($match->score->fulltime)) {
                continue;
            }

            $isHome = ($match->teams->home->id == $teamId);
            $goalsScored = $isHome
                ? ($match->score->fulltime->home ?? 0)
                : ($match->score->fulltime->away ?? 0);

            if ($goalsScored >= 2) {
                $over++;
            }
        }

        $rate = $total > 0 ? $over / $total : 0;

        if ($rate >= 0.65 && $total >= 5) {
            $result['tip'] = $side;
            $result['confidence'] = round($rate * 100);
        }

        return $result;
    }

    /**
     * Calculate team performance statistics for DNB analysis
     */
    private function calculateTeamPerformance($matches, $teamId): array
    {
        $stats = [
            'total_games' => 0,
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'win_rate' => 0,
            'draw_rate' => 0,
            'loss_rate' => 0,
            'recent_form_wins' => 0
        ];

        if (empty($matches) || count($matches) < 4) {
            return $stats;
        }

        // Take last 8 games for analysis
        $recentMatches = array_slice($matches, -8);
        $stats['total_games'] = count($recentMatches);

        // Analyze last 5 games for recent form
        $lastFiveMatches = array_slice($recentMatches, -5);

        foreach ($recentMatches as $match) {
            if (!isset($match->score->fulltime)) {
                continue;
            }

            $homeScore = $match->score->fulltime->home ?? 0;
            $awayScore = $match->score->fulltime->away ?? 0;
            $isHome = ($match->teams->home->id == $teamId);

            // Determine result from team's perspective
            if ($homeScore == $awayScore) {
                $stats['draws']++;
            } elseif (($isHome && $homeScore > $awayScore) || (!$isHome && $awayScore > $homeScore)) {
                $stats['wins']++;
            } else {
                $stats['losses']++;
            }
        }

        // Calculate recent form (last 5 games)
        foreach ($lastFiveMatches as $match) {
            if (!isset($match->score->fulltime)) {
                continue;
            }

            $homeScore = $match->score->fulltime->home ?? 0;
            $awayScore = $match->score->fulltime->away ?? 0;
            $isHome = ($match->teams->home->id == $teamId);

            if (($isHome && $homeScore > $awayScore) || (!$isHome && $awayScore > $homeScore)) {
                $stats['recent_form_wins']++;
            }
        }

        // Calculate rates
        if ($stats['total_games'] > 0) {
            $stats['win_rate'] = $stats['wins'] / $stats['total_games'];
            $stats['draw_rate'] = $stats['draws'] / $stats['total_games'];
            $stats['loss_rate'] = $stats['losses'] / $stats['total_games'];
        }

        return $stats;
    }

}
