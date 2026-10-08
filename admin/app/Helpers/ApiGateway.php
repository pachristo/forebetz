<?php

namespace App\Helpers;

class ApiGateway
{
    public static function callAPI($endpoint, $type='football')
    {
        $base = $type=='football' ? env('API_FOOTBALL_BASE_URL') : env('API_BASKETBALL_BASE_URL');

        return \Unirest\Request::get(
            $base.'/'.$endpoint,
            array(
                "x-rapidapi-host" => env('API_FOOTBALL_HOST'),
                "x-rapidapi-key" => env('API_FOOTBALL_KEY')
            )
        );
    }



    public  function loadLastMatch($id)
    {
        $payload_api = self::callAPI('fixtures?id=' . $id);
        $item = $payload_api->body->response[0];

        $last_home = self::callAPI('fixtures?last=38&team=' . $item->teams->home->id);
        $last_home = $last_home->body->response;
        $last_away = self::callAPI('fixtures?last=38&team=' . $item->teams->away->id);
        $last_away = $last_away->body->response;


            $prob_ab = $this->getOverallProbabilityWithCategories($item->teams->home->id, $last_home, $item->teams->away->id, $last_away, []);
           \App\APIprediction::updateOrCreate(['match_id' => $item->fixture->id], [
            'match_id' => $item->fixture->id,

            // 'pred_data' => $pred,
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

//    public static function callAPI($filters = [])
//    {
//        $params = array(
//            "APIkey" => env('API_FOOTBALL_KEY')
//        );
//        foreach ($filters as $key => $val) $params[$key] = $val;
//
//        return \Unirest\Request::get(
//            env('API_FOOTBALL_BASE_URL'),
//            [],
//            $params
//        )->body;
//    }
}
