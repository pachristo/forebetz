<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fixture;
use App\Models\Prediction;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class FreeTipsController extends Controller
{
    /**
     * Grade predictions on finished fixtures.
     *
     * {@code GET /mark-result?reset_all=1} — sets {@code status} and {@code winning_status} to {@code 0}
     * for **every** prediction row (full reset). Destructive; use before a clean re-grade.
     *
     * {@code GET /mark-result?revert=1} — resets only rows with {@code winning_status} in (1, 2) to 0/0.
     *
     * Pending rows: {@code status} null/0 and {@code winning_status} null/0, fixture FT/AET/PEN.
     * Win: {@code winning_status = 1}, {@code status = 1}. Loss: {@code winning_status = 2}, {@code status = 0}.
     *
     * Full-time goals prefer {@code ft_home_goals}/{@code ft_away_goals} when set (same as free-picks display),
     * then fall back to {@code home_goal}/{@code away_goal} — using only {@code home_goal} caused 0–0 and all losses.
     */
    public function MarkResult(Request $request)
    {
        $finished = ['FT', 'AET', 'PEN'];

        if ($request->boolean('reset_all') || $request->query('reset_all') === '1') {
            $updated = Prediction::query()->update([
                'winning_status' => '0',
                'status' => '0',
            ]);

            return response()->json([
                'status' => 'success',
                'mode' => 'reset_all',
                'updated_rows' => $updated,
                'hint' => 'All predictions reset. Call /mark-result without query params to grade finished fixtures.',
            ]);
        }

        if ($request->boolean('revert') || $request->query('revert') === '1') {
            $reverted = Prediction::query()
                ->whereIn('winning_status', ['1', '2'])
                ->update([
                    'winning_status' => '0',
                    'status' => '0',
                ]);

            return response()->json([
                'status' => 'success',
                'mode' => 'revert',
                'reverted_rows' => $reverted,
                'hint' => 'Call /mark-result without revert to grade again.',
            ]);
        }

        $predictions = Prediction::query()
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '0')->orWhere('status', 0);
            })
            ->where(function ($q) {
                $q->whereNull('winning_status')
                    ->orWhere('winning_status', '0')
                    ->orWhere('winning_status', '');
            })
            ->whereHas('fixture', function ($q) use ($finished) {
                $q->whereIn('match_status', $finished);
            })
            ->with('fixture')
            ->get();

        $wins = 0;
        $losses = 0;
        $skipped = 0;

        foreach ($predictions as $prediction) {
            $fixture = $prediction->fixture;
            if (! $fixture || ! in_array((string) $fixture->match_status, $finished, true)) {
                $skipped++;

                continue;
            }

            $conditions = $this->buildTipConditionsMap($fixture);
            $tipKey = $this->normalizeTipKey((string) $prediction->tips);

            $evaluated = $this->evaluateTipAgainstFixture($tipKey, $conditions, $fixture);
            if ($evaluated === null) {
                $skipped++;

                continue;
            }

            if ($evaluated) {
                $prediction->winning_status = '1';
                $prediction->status = '1';
                $wins++;
            } else {
                $prediction->winning_status = '2';
                $prediction->status = '0';
                $losses++;
            }

            $prediction->save();
        }

        return response()->json([
            'status' => 'success',
            'graded' => $wins + $losses,
            'wins' => $wins,
            'losses' => $losses,
            'skipped' => $skipped,
        ]);
    }

    /**
     * Full-time goals for grading (aligned with {@see \App\Services\FreePicksNewNiceLoader::displayScore}).
     *
     * @return array{0: float, 1: float}
     */
    private function resolveFullTimeGoals(Fixture $v): array
    {
        $ms = strtoupper(trim((string) ($v->match_status ?? '')));
        $finished = in_array($ms, ['FT', 'AET', 'PEN', 'WO'], true);

        if ($finished) {
            $fh = $v->ft_home_goals;
            $fa = $v->ft_away_goals;
            if ($fh !== null && $fh !== '' && $fa !== null && $fa !== '') {
                return [(float) $fh, (float) $fa];
            }
        }

        $h = $v->home_goal;
        $a = $v->away_goal;

        return [(float) ($h ?? 0), (float) ($a ?? 0)];
    }

    private function normalizeTipKey(string $raw): string
    {
        $t = strtolower(trim($raw));
        $t = preg_replace('/\s+/u', '', $t);

        return match ($t) {
            'home' => '1',
            'away' => '2',
            'draw' => 'x',
            default => $t,
        };
    }

    /**
     * @return array<string, string> tip key => '1' pass | '0' fail
     */
    private function buildTipConditionsMap(Fixture $v): array
    {
        $ht_home = (float) ($v->ht_home_goals ?? 0);
        $ht_away = (float) ($v->ht_away_goals ?? 0);
        $ht_total = $ht_home + $ht_away;

        [$homeGoals, $awayGoals] = $this->resolveFullTimeGoals($v);
        $totalGoals = $homeGoals + $awayGoals;

        return [
            '1' => $homeGoals > $awayGoals ? '1' : '0',
            '2' => $homeGoals < $awayGoals ? '1' : '0',
            'x' => $homeGoals == $awayGoals ? '1' : '0',
            '1x' => $homeGoals >= $awayGoals ? '1' : '0',
            'x2' => $awayGoals >= $homeGoals ? '1' : '0',
            '12' => $homeGoals != $awayGoals ? '1' : '0',

            'dnd1' => $homeGoals > $awayGoals ? '1' : '0',
            'dnd2' => $awayGoals > $homeGoals ? '1' : '0',

            '+0.5' => $ht_total > 0.5 ? '1' : '0',
            '-0.5' => $ht_total < 1 ? '1' : '0',
            'fh-1.5' => $ht_total < 2 ? '1' : '0',
            'fh+1.5' => $ht_total > 1.5 ? '1' : '0',
            'fh-0.5' => $ht_total < 1 ? '1' : '0',
            'fh+0.5' => $ht_total > 0.5 ? '1' : '0',
            'ht-0.5' => $ht_total < 1 ? '1' : '0',
            'ht+0.5' => $ht_total > 0.5 ? '1' : '0',
            'ht-1.5' => $ht_total < 2 ? '1' : '0',
            'ht+1.5' => $ht_total > 1.5 ? '1' : '0',
            'fh-2.5' => $ht_total < 2.5 ? '1' : '0',
            'fh+2.5' => $ht_total > 2.5 ? '1' : '0',

            '-1.5' => $totalGoals < 1.5 ? '1' : '0',
            '+1.5' => $totalGoals > 1.5 ? '1' : '0',
            '-2.5' => $totalGoals < 2.5 ? '1' : '0',
            '+2.5' => $totalGoals > 2.5 ? '1' : '0',
            '-3.5' => $totalGoals < 3.5 ? '1' : '0',
            '+3.5' => $totalGoals > 3.5 ? '1' : '0',

            // HTo/ATo = team goals in the first half (same as new_admin).
            'hto0.5' => $ht_home > 0.5 ? '1' : '0',
            'htu0.5' => $ht_home < 0.5 ? '1' : '0',
            'hto1.5' => $ht_home > 1.5 ? '1' : '0',
            'htu1.5' => $ht_home < 1.5 ? '1' : '0',
            'hto2.5' => $ht_home > 2.5 ? '1' : '0',
            'htu2.5' => $ht_home < 2.5 ? '1' : '0',

            'ato0.5' => $ht_away > 0.5 ? '1' : '0',
            'atu0.5' => $ht_away < 0.5 ? '1' : '0',
            'ato1.5' => $ht_away > 1.5 ? '1' : '0',
            'atu1.5' => $ht_away < 1.5 ? '1' : '0',
            'ato2.5' => $ht_away > 2.5 ? '1' : '0',
            'atu2.5' => $ht_away < 2.5 ? '1' : '0',

            'yes' => $homeGoals > 0 && $awayGoals > 0 ? '1' : '0',
            'no' => ($homeGoals == 0 || $awayGoals == 0) ? '1' : '0',

            '1/1' => $ht_home > $ht_away && $homeGoals > $awayGoals ? '1' : '0',
            '2/2' => $ht_away > $ht_home && $awayGoals > $homeGoals ? '1' : '0',
            'x/1' => $ht_home == $ht_away && $homeGoals > $awayGoals ? '1' : '0',
            '1/2' => $ht_home > $ht_away && $awayGoals > $homeGoals ? '1' : '0',
            '2/1' => $ht_away > $ht_home && $homeGoals > $awayGoals ? '1' : '0',
            'x/x' => $ht_home == $ht_away && $homeGoals == $awayGoals ? '1' : '0',
            '1/x' => $ht_home > $ht_away && $homeGoals == $awayGoals ? '1' : '0',
            '2/x' => $ht_away > $ht_home && $homeGoals == $awayGoals ? '1' : '0',
            'x/2' => $ht_home == $ht_away && $awayGoals > $homeGoals ? '1' : '0',

            // Win either half = won 1st half OR won 2nd half (not full-time).
            'hweh' => ($ht_home > $ht_away || ($homeGoals - $ht_home) > ($awayGoals - $ht_away)) ? '1' : '0',
            'aweh' => ($ht_away > $ht_home || ($awayGoals - $ht_away) > ($homeGoals - $ht_home)) ? '1' : '0',

            'ht1' => $ht_home > $ht_away ? '1' : '0',
            'ht2' => $ht_away > $ht_home ? '1' : '0',
            'htx' => $ht_home == $ht_away ? '1' : '0',
        ];
    }

    /**
     * @param  array<string, string>  $conditions
     */
    private function evaluateTipAgainstFixture(string $tipKey, array $conditions, Fixture $fixture): ?bool
    {
        if ($tipKey === '') {
            return null;
        }

        if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $tipKey, $m)) {
            [$homeGoals, $awayGoals] = $this->resolveFullTimeGoals($fixture);

            return (int) $homeGoals === (int) $m[1] && (int) $awayGoals === (int) $m[2];
        }

        if (! isset($conditions[$tipKey])) {
            return null;
        }

        return $conditions[$tipKey] === '1';
    }

    public function loadFreeTips(Request $request)
    {
        try {
            // Get date from request, default to today
            $p=(int)request()->p;
            $date = $request->get('p', Carbon::now()->addDays($p))->format('Y-m-d');
            
         

            // Fetch fixtures with free predictions
            $fixtures = Fixture::whereDate('match_date', $date)
                ->whereHas('prediction', function ($query) {
                    $query->where('prob','>=', '70');
                })
                ->with([
                    'prediction' => function ($query) {
                       $query->where('prob','>=', '70');

                    },
                  
                    
                    'odds'
                ])
                ->orderBy('match_time')
                ->get()
                ;

            // Format the data
          foreach ($fixtures as $fixture) {
               
            $matchId=$fixture->match_id;
            foreach($fixture->prediction as $k=>$v){
                if((float)$v->prob < 70){
                    continue;
                }
                \App\Models\Prediction::updateOrCreate(['match_id'=>$matchId,'type'=>'free'],[
                    'match_id'=>$matchId,
                    'type'=>'free',
                      'vip_type' => 'regular',
                    'tips' => $v->tips,
                    'odds' => $v->odds,
                    'prob' => $v->prob,
                ]);
            }

            }

            return response()->json([
                'status' => 'success',
                'date' => $date,
           
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load free tips',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function loadAccaTips(Request $request)
    {
        try {
            // Get date from request, default to today
         $p=(int)request()->p;
            $date = $request->get('p', Carbon::now()->addDays($p))->format('Y-m-d');
            
         

            // Fetch fixtures with free predictions
            $fixtures = Fixture::whereDate('match_date', $date)
                ->whereHas('prediction', function ($query) {
                    $query->where('prob','>=', '78');
                })
                ->with([
                    'prediction' => function ($query) {
                       $query->where('prob','>=', '78');

                    },
                  
                    
                    'odds'
                ])
                ->orderBy('match_time')
                ->get()
                ;

            // Format the data
          foreach ($fixtures as $fixture) {
               
            $matchId=$fixture->match_id;
            foreach($fixture->prediction as $k=>$v){
                if((float)$v->prob < 70){
                    continue;
                }
                \App\Models\Prediction::updateOrCreate(['match_id'=>$matchId,'type'=>'acca'],[
                    'match_id'=>$matchId,
                    'type'=>'acca',
                      'vip_type' => 'regular',
                    'tips' => $v->tips,
                    'odds' => $v->odds,
                    'prob' => $v->prob,
                ]);
            }

            }

            return response()->json([
                'status' => 'success',
                'date' => $date,
           
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load free tips',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function loadBankerTips(Request $request){
        try {
            // Get date from request, default to today
            $p=(int)request()->p;
            $date = $request->get('p', Carbon::now()->addDays($p))->format('Y-m-d');
            
         

            // Fetch fixtures with free predictions
            $fixtures = Fixture::whereDate('match_date', $date)
                ->whereHas('prediction', function ($query) {
                    $query->where('prob','>=', '80');
                })
                ->with([
                    'prediction' => function ($query) {
                       $query->where('prob','>=', '80');

                    },
                  
                    
                    'odds'
                ])
              
                ->paginate(5)
                ;

            // Format the data
          foreach ($fixtures as $fixture) {
               
            $matchId=$fixture->match_id;
            foreach($fixture->prediction as $k=>$v){
                if((float)$v->prob < 80){
                    continue;
                }
                \App\Models\Prediction::updateOrCreate(['match_id'=>$matchId,'type'=>'banker'],[
                    'match_id'=>$matchId,
                    'type'=>'banker',
                      'vip_type' => 'regular',
                    'tips' => $v->tips,
                    'odds' => $v->odds,
                    'prob' => $v->prob,
                ]);
            }

            }

            return response()->json([
                'status' => 'success',
                'date' => $date,
           
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load free tips',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get free tips for multiple dates
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFreeTipsRange(Request $request)
    {
      
    }

    /**
     * Get statistics for free tips
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFreeTipsStats(Request $request)
    {
     
    }
}