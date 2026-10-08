<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiFootball
{
    private const TTL_HOURS = 6;

    /**
     * League table for one season. Cups with several groups return the group containing $teamId
     * when given, otherwise the first group.
     *
     * @return list<array{pos: int, team_id: int, club: string, logo: string, p: int, w: int, d: int, l: int, gf: int, ga: int, gd: int, pts: int, form: list<string>, zone: string}>
     */
    public function standings(int $leagueId, ?int $season = null, ?int $teamId = null): array
    {
        $season ??= $this->season();

        $groups = $this->cached("standings.v2.{$leagueId}.{$season}", function () use ($leagueId, $season) {
            $groups = $this->get('standings', ['league' => $leagueId, 'season' => $season])[0]['league']['standings'] ?? [];

            return array_map(fn (array $table) => array_map(fn (array $row) => [
                'pos' => (int) $row['rank'],
                'team_id' => (int) $row['team']['id'],
                'club' => (string) $row['team']['name'],
                'logo' => (string) $row['team']['logo'],
                'p' => (int) ($row['all']['played'] ?? 0),
                'w' => (int) ($row['all']['win'] ?? 0),
                'd' => (int) ($row['all']['draw'] ?? 0),
                'l' => (int) ($row['all']['lose'] ?? 0),
                'gf' => (int) ($row['all']['goals']['for'] ?? 0),
                'ga' => (int) ($row['all']['goals']['against'] ?? 0),
                'gd' => (int) ($row['goalsDiff'] ?? 0),
                'pts' => (int) ($row['points'] ?? 0),
                'form' => str_split(strtolower(substr((string) ($row['form'] ?? ''), -5))) ?: [],
                'zone' => (string) ($row['description'] ?? ''),
            ], $table), $groups);
        });

        if ($teamId) {
            foreach ($groups as $table) {
                if (in_array($teamId, array_column($table, 'team_id'), true)) {
                    return $table;
                }
            }
        }

        return $groups[0] ?? [];
    }

    /**
     * A team's most recent finished fixtures.
     *
     * @return list<array{id: int, date: string, league: string, home: string, home_logo: string, away: string, away_logo: string, home_goals: ?int, away_goals: ?int, home_won: ?bool, away_won: ?bool}>
     */
    public function lastFixtures(int $teamId, int $count = 5): array
    {
        return $this->cached("team-last.{$teamId}.{$count}", fn () => array_map(fn (array $row) => self::fixtureRow($row), $this->get('fixtures', ['team' => $teamId, 'last' => $count])));
    }

    /** @return array{id: int, date: string, league: string, home: string, home_logo: string, away: string, away_logo: string, home_goals: ?int, away_goals: ?int, home_won: ?bool, away_won: ?bool} */
    public static function fixtureRow(array $row): array
    {
        return [
            'id' => (int) ($row['fixture']['id'] ?? 0),
            'date' => isset($row['fixture']['date']) ? date('M j, Y', strtotime($row['fixture']['date'])) : '',
            'league' => trim(($row['league']['country'] ?? '').': '.($row['league']['name'] ?? ''), ': '),
            'home' => (string) ($row['teams']['home']['name'] ?? ''),
            'home_logo' => (string) ($row['teams']['home']['logo'] ?? ''),
            'away' => (string) ($row['teams']['away']['name'] ?? ''),
            'away_logo' => (string) ($row['teams']['away']['logo'] ?? ''),
            'home_goals' => isset($row['goals']['home']) ? (int) $row['goals']['home'] : null,
            'away_goals' => isset($row['goals']['away']) ? (int) $row['goals']['away'] : null,
            'home_won' => $row['teams']['home']['winner'] ?? null,
            'away_won' => $row['teams']['away']['winner'] ?? null,
        ];
    }

    /** @return list<array{player_id: int, player: string, photo: string, club: string, logo: string, goals: int}> */
    public function topScorers(int $leagueId, ?int $season = null): array
    {
        $season ??= $this->season();

        return $this->cached("topscorers.{$leagueId}.{$season}", fn () => array_map(function (array $row) {
            $stats = $row['statistics'][0] ?? [];

            return [
                'player_id' => (int) $row['player']['id'],
                'player' => (string) $row['player']['name'],
                'photo' => (string) ($row['player']['photo'] ?? ''),
                'club' => (string) ($stats['team']['name'] ?? ''),
                'logo' => (string) ($stats['team']['logo'] ?? ''),
                'goals' => (int) ($stats['goals']['total'] ?? 0),
            ];
        }, $this->get('players/topscorers', ['league' => $leagueId, 'season' => $season])));
    }

    /**
     * Every fixture in play right now, keyed by fixture id. Short-lived cache; an empty list is a valid answer.
     *
     * @return array<int, array{status: string, elapsed: ?int, extra: ?int, home_goals: ?int, away_goals: ?int}>
     */
    public function liveFixtures(): array
    {
        return Cache::remember('api-football.live', now()->addSeconds(60), function () {
            try {
                $rows = $this->get('fixtures', ['live' => 'all']);
            } catch (Throwable $e) {
                Log::warning("API-Football live failed: {$e->getMessage()}");

                return [];
            }

            $live = [];
            foreach ($rows as $row) {
                $live[(int) $row['fixture']['id']] = [
                    'status' => (string) ($row['fixture']['status']['short'] ?? ''),
                    'elapsed' => $row['fixture']['status']['elapsed'] ?? null,
                    'extra' => $row['fixture']['status']['extra'] ?? null,
                    'home_goals' => $row['goals']['home'] ?? null,
                    'away_goals' => $row['goals']['away'] ?? null,
                ];
            }

            return $live;
        });
    }

    /**
     * Events and team statistics for one fixture; refreshed every minute while live.
     *
     * @return array{events: list<array<string, mixed>>, stats: list<array{label: string, home: string, away: string, home_pct: int}>}
     */
    public function fixtureDetail(int $fixtureId, bool $live): array
    {
        $empty = ['events' => [], 'stats' => []];

        return Cache::remember("api-football.fixture.{$fixtureId}", $live ? now()->addSeconds(60) : now()->addHours(self::TTL_HOURS), function () use ($fixtureId, $empty) {
            try {
                $row = $this->get('fixtures', ['id' => $fixtureId])[0] ?? null;
            } catch (Throwable $e) {
                Log::warning("API-Football fixture {$fixtureId} failed: {$e->getMessage()}");

                return $empty;
            }

            if (! $row) {
                return $empty;
            }

            $homeId = (int) ($row['teams']['home']['id'] ?? 0);

            $events = array_map(fn (array $e) => [
                'minute' => (int) ($e['time']['elapsed'] ?? 0),
                'extra' => $e['time']['extra'] ?? null,
                'side' => (int) ($e['team']['id'] ?? 0) === $homeId ? 'home' : 'away',
                'type' => strtolower((string) ($e['type'] ?? '')),
                'detail' => (string) ($e['detail'] ?? ''),
                'player' => (string) ($e['player']['name'] ?? ''),
                'assist' => (string) ($e['assist']['name'] ?? ''),
            ], $row['events'] ?? []);

            $byTeam = [];
            foreach ($row['statistics'] ?? [] as $team) {
                $side = (int) ($team['team']['id'] ?? 0) === $homeId ? 'home' : 'away';
                foreach ($team['statistics'] ?? [] as $stat) {
                    $byTeam[$stat['type']][$side] = $stat['value'];
                }
            }

            $stats = [];
            foreach (['Ball Possession' => 'Possession', 'expected_goals' => 'Expected goals (xG)', 'Total Shots' => 'Total shots', 'Shots on Goal' => 'Shots on target', 'Corner Kicks' => 'Corners', 'Fouls' => 'Fouls', 'Offsides' => 'Offsides', 'Yellow Cards' => 'Yellow cards', 'Red Cards' => 'Red cards', 'Passes %' => 'Pass accuracy'] as $type => $label) {
                if (! isset($byTeam[$type])) {
                    continue;
                }

                $home = $byTeam[$type]['home'] ?? 0;
                $away = $byTeam[$type]['away'] ?? 0;
                $h = (float) $home;
                $a = (float) $away;

                $stats[] = [
                    'label' => $label,
                    'home' => (string) ($home ?? 0),
                    'away' => (string) ($away ?? 0),
                    'home_pct' => $h + $a > 0 ? (int) round($h / ($h + $a) * 100) : 50,
                ];
            }

            return ['events' => $events, 'stats' => $stats];
        });
    }

    /** European seasons start in July; API-Football names a season by its starting year. */
    public function season(): int
    {
        $now = now();

        return $now->month >= 7 ? $now->year : $now->year - 1;
    }

    /**
     * Cache fresh results for a few hours and keep the last good copy, so an API outage or
     * an empty response never blanks the widget.
     */
    private function cached(string $key, callable $fetch): array
    {
        $fresh = "api-football.{$key}";
        $stale = "{$fresh}.last-good";

        if (($hit = Cache::get($fresh)) !== null) {
            return $hit;
        }

        try {
            $data = $fetch();
        } catch (Throwable $e) {
            Log::warning("API-Football {$key} failed: {$e->getMessage()}");
            $data = [];
        }

        if ($data) {
            Cache::put($fresh, $data, now()->addHours(self::TTL_HOURS));
            Cache::forever($stale, $data);

            return $data;
        }

        $fallback = Cache::get($stale, []);
        Cache::put($fresh, $fallback, now()->addMinutes(15));

        return $fallback;
    }

    private function get(string $endpoint, array $query): array
    {
        $key = config('services.football.api_key');

        if (! $key) {
            return [];
        }

        $response = Http::baseUrl(config('services.football.base_url'))
            ->withHeaders(['x-apisports-key' => $key])
            ->timeout(8)
            ->retry(2, 300, throw: false)
            ->get($endpoint, $query);

        if ($response->failed() || ! empty($response->json('errors'))) {
            throw new \RuntimeException('HTTP '.$response->status().' '.json_encode($response->json('errors')));
        }

        return $response->json('response') ?? [];
    }
}
