<?php

namespace App\Services;

use App\Helpers\ApiGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Mockery\Exception;

class FixtureService
{

    public static function extractFixtureData($data, $type): void
    {
        $items = [];
        foreach ($data as $key => $item) {
            $type == 'football' && $items[$item->fixture->id] = [
                'match_id' => $item->fixture->id,
                'match_date' => Carbon::parse($item->fixture->date)->format('Y-m-d'),
                'match_time' => Carbon::parse($item->fixture->date)->format('H:i'),
                'teamOne' => $item->teams->home->name,
                'teamOneId' => $item->teams->home->id,
                'teamTwo' => $item->teams->away->name,
                'teamTwoId' => $item->teams->away->id,
                'league_id' => $item->league->id,
                'league' => $item->league->name,
                'season' => $item->league->season,
                'country_name' => $item->league->country,

                'home_flag' => $item->teams->home->logo,
                'away_flag' => $item->teams->away->logo,
                'league_flag' => $item->league->logo,
                'country_flag' => $item->league->flag,

                'match_stadium' => $item->fixture->venue->name,
                'match_referee' => $item->fixture->referee,

                'home_fulltime_score' => $item->score->fulltime->home,
                'away_fulltime_score' => $item->score->fulltime->away,
                'home_halftime_score' => $item->score->halftime->home,
                'away_halftime_score' => $item->score->halftime->away,
                'home_extratime_score' => $item->score->extratime->home,
                'away_extratime_score' => $item->score->extratime->away,
                'home_penalty_score' => $item->score->penalty->home,
                'away_penalty_score' => $item->score->penalty->away,

                //                'match_hometeam_system' => $item->match_hometeam_system,
//                'match_awayteam_system' => $item->match_awayteam_system,
//                'predictions' => (array)$item
            ];
        }
        Session::put('fixtures', $items);
        //        return $items;
    }


    public static function processFixtureForDataTable($fixtures): array
    {
        $items = [];
        $sn = 1;

        foreach ($fixtures as $key => $fixture) {
            $items[] = [
                'sn' => $sn++,
                'league' => $fixture['country_name'] . ': ' . $fixture['league'],
                'fixture' => $fixture['teamOne'] . ' vs ' . $fixture['teamTwo'],
                'date' => Carbon::parse($fixture['match_date'])->format('d-m-Y'),
                'time' => $fixture['match_time'],
                'action' => [
                        'id' => $fixture['match_id'],
                        'home' => $fixture['teamOne'],
                        'away' => $fixture['teamTwo'],
                    ]
            ];
        }

        return $items;
    }

    public static function getOdds($matchId): array
    {
        $odds = ApiGateway::callAPI('odds?fixture=' . $matchId . '&bookmaker=11');

        if (!count($odds->body->response))
            return [];

        return $odds->body->response;
    }

    public function getPreviousGames($fixture): array
    {
        $homeForm = '';
        $awayForm = '';
        try {
            $h2h = ApiGateway::callAPI([
                'action' => 'get_H2H',
                'firstTeam' => $fixture->match_hometeam_name,
                'secondTeam' => $fixture->match_awayteam_name,
                'firstTeamId' => (int) $fixture->match_hometeam_id,
                'secondTeamId' => (int) $fixture->match_awayteam_id,
            ]);

            $homeForm = $this->determineForm($h2h->firstTeam_lastResults ?? [], $fixture->match_hometeam_id);
            $awayForm = $this->determineForm($h2h->secondTeam_lastResults ?? [], $fixture->match_awayteam_id);

        } catch (\Throwable $e) {
            info($e->getMessage());
        }

        return [$homeForm, $awayForm];
    }

    private function determineForm($lastMatches, $teamId): string
    {
        $forms = [];

        foreach (array_slice($lastMatches, 0, 5) as $match) {
            if ((int) $match->match_hometeam_score == (int) $match->match_awayteam_score) {
                $forms[] = 'D';
            } elseif ((int) $match->match_hometeam_score > (int) $match->match_awayteam_score) {
                if ($teamId == $match->match_hometeam_id) {
                    $forms[] = 'W';
                } else {
                    $forms[] = 'L';
                }
            } else {
                if ($teamId == $match->match_awayteam_id) {
                    $forms[] = 'W';
                } else {
                    $forms[] = 'L';
                }
            }
        }

        return implode('', $forms);
    }

    public function predictionEngine($data, $market): string|null
    {
        $tip = null;
        $data = $data[0];
        $prediction = $data->predictions;

        if ($market == '1_5_goals' && $prediction->under_over != null) {
            if (str_contains($prediction->under_over, '1.5')) {
                if (str_contains($prediction->under_over, '-'))
                    $tip = str_replace('-', 'Under ', $prediction->under_over);
                elseif (str_contains($prediction->under_over, '+'))
                    $tip = str_replace('+', 'Over ', $prediction->under_over);
            }
        } elseif ($market == '2_5_goals' && $prediction->under_over != null) {
            if (str_contains($prediction->under_over, '2.5')) {
                if (str_contains($prediction->under_over, '+'))
                    $tip = str_replace('+', 'Over ', $prediction->under_over);
            }
        } elseif ($market == '3_5_goals' && $prediction->under_over != null) {
            if (str_contains($prediction->under_over, '3.5')) {
                if (str_contains($prediction->under_over, '+'))
                    $tip = str_replace('+', 'Over ', $prediction->under_over);
            }
        } elseif ($market == 'double_chance' && $prediction->win_or_draw) {
            $homeId = $data->teams->home->id;
            if ($prediction->winner->id == $homeId)
                $tip = '1X';
            else
                $tip = 'X2';
        } elseif ($market == 'home_win' && str_starts_with($prediction->advice, 'Winner')) {
            $homeId = $data->teams->home->id;
            if ($prediction->winner->id == $homeId)
                $tip = '1';
        } elseif ($market == 'away_win' && str_starts_with($prediction->advice, 'Winner')) {
            $awayId = $data->teams->away->id;
            if ($prediction->winner->id == $awayId)
                $tip = '2';
        } elseif ($market == 'both_teams_score') {
            $accepted = ['-3.5', '-4.5', '-5.5', '-6.5', '-7.5', '-8.5', '-9.5', '-10.5'];
            if (in_array($prediction->goals->home, $accepted) && in_array($prediction->goals->away, $accepted))
                $tip = 'yes';
        } elseif ($market == 'draws') {
            if (
                $data->comparison->total->away >= 40 &&
                $data->comparison->total->home >= 40 &&
                $data->predictions->goals->home = '-1.5' && $data->predictions->goals->away == '-1.5'
            ) {
                $tip = 'X';
            }

            //            $home_percent = (int)str_replace('%', '', $prediction->percent->home);
//            $draw_percent = (int)str_replace('%', '', $prediction->percent->draw);
//            $away_percent = (int)str_replace('%', '', $prediction->percent->away);

            //            $array = array($home_percent, $draw_percent, $away_percent);
//            $max = max($array);
//            $max_index = array_search($max, $array);
//
//            $occurrence = count(array_keys($array, $max));

            //            if ($draw_percent >= 50) $tip = 'X';
        } elseif ($market == 'win_either_half') {
            // WEH predictions are now handled by performance analysis in AutoPredictService
            // This market is kept for compatibility but not used
            $tip = null;
        } elseif ($market == 'draw_no_bet') {
            // DNB predictions are now handled by performance analysis in AutoPredictService
            // This market is kept for compatibility but not used
            $tip = null;
        }

        return $tip;
    }


    public function extractOdd($odds, $market, $tip): string|null
    {
        if (count($odds) == 0)
            return null;

        $odd = null;
        $oddKey = marketOddKeys()[$market];
        $odds = $odds[0]->bookmakers[0]->bets;

        $bet = array_filter($odds, function ($bet) use ($oddKey) {
            return $bet->name == $oddKey;
        });

        if (!$bet)
            return null;

        $bet = reset($bet);
        if ($market == 'home_win')
            $odd = $bet->values[0]->odd;
        elseif ($market == 'draws')
            $odd = $bet->values[1]->odd;
        elseif ($market == 'away_win')
            $odd = $bet->values[2]->odd;
        elseif ($market == 'both_teams_score')
            $odd = $bet->values[0]->odd;
        elseif ($market == 'double_chance') {
            if ($tip == '1X')
                $odd = $bet->values[0]->odd;
            elseif ($tip == 'X2')
                $odd = $bet->values[2]->odd;
        } elseif ($market == 'win_either_half') {
            if ($tip == 'HWEH')
                $odd = $bet->values[0]->odd; // Home Win Either Half
            elseif ($tip == 'AWEH')
                $odd = $bet->values[1]->odd; // Away Win Either Half
        } elseif ($market == 'draw_no_bet') {
            if ($tip == '1 DNB')
                $odd = $bet->values[0]->odd; // Home Draw No Bet
            elseif ($tip == '2 DNB')
                $odd = $bet->values[1]->odd; // Away Draw No Bet
        }
        //        elseif ($market=='1_5_goals') {
//            if ($tip=='Over 1.5') $odd = $bet->values[0]->odd;
//            elseif ($tip=='Under 1.5') $odd = $bet->values[1]->odd;
//        }
        elseif ($market == '2_5_goals' || $market == '1_5_goals') {
            $oddFinder = array_filter($bet->values, function ($value) use ($tip) {
                return $value->value == $tip;
            });
            if (!$oddFinder)
                return null;
            $oddFinder = reset($oddFinder);
            $odd = $oddFinder->odd;
            //            if ($tip=='Over 2.5') $odd = $bet?->values[8]?->odd;
//            elseif ($tip=='Under 2.5') $odd = $bet?->values[9]?->odd;
        }

        return $odd;
    }

    public function get1X2Odds($odds): array
    {
        $homeOdd = null;
        $drawOdd = null;
        $awayOdd = null;

        if (count($odds) == 0)
            return [$homeOdd, $drawOdd, $awayOdd];

        $odds = $odds[0]->bookmakers[0]->bets;

        $bet = array_filter($odds, function ($bet) {
            return $bet->name == 'Match Winner';
        });

        if (!$bet)
            return [$homeOdd, $drawOdd, $awayOdd];

        $bet = reset($bet);
        $homeOdd = $bet->values[0]->odd;
        $drawOdd = $bet->values[1]->odd;
        $awayOdd = $bet->values[2]->odd;

        return [$homeOdd, $drawOdd, $awayOdd];
    }

    public function calcProb($odds): float
    {
        return number_format((1 / ($odds == 0 ? 1 : $odds)) * 100, 2);
    }

    public function getLast5Form($form): string
    {
        if ($form)
            return substr($form, -5);
        return '';
    }
}
