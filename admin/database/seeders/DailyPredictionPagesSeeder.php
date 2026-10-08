<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

/**
 * "Predictions by day" pages for the public site (footer → Daily Predictions).
 * Safe to re-run: rows are matched by slug and refreshed.
 */
class DailyPredictionPagesSeeder extends Seeder
{
    private const SITE = 'Dailysuretips';

    public const CATEGORY = 'Daily predictions';

    public function run(): void
    {
        SeoPage::unguarded(function (): void {
            foreach ($this->days() as $slug => [$label, $focus]) {
                SeoPage::query()->updateOrCreate(['slug' => $slug], $this->page($label, $focus) + [
                    'category' => self::CATEGORY,
                    'status' => 'published',
                    'date' => now(),
                ]);
            }
        });
    }

    /** @return array<string, array{0: string, 1: string}> */
    private function days(): array
    {
        return [
            'football-predictions-for-monday' => ['Monday', 'Monday cards are lighter, so we focus on the leagues that still play — the Championship, Scandinavian and South American divisions, plus rearranged top-flight games. Smaller slates mean more time spent on each fixture and fewer, sharper selections.'],
            'football-predictions-for-tuesday' => ['Tuesday', 'Tuesday brings Champions League nights in season, alongside midweek rounds in the English Football League and international breaks. Rotation is common before and after European games, so team news carries extra weight in our Tuesday picks.'],
            'football-predictions-for-wednesday' => ['Wednesday', 'Wednesday is a busy midweek day with the second round of Champions League fixtures, domestic cup ties and league catch-up games. Fatigue from weekend matches and travel is a key filter we apply before backing any side.'],
            'football-predictions-for-thursday' => ['Thursday', 'Thursday belongs to the Europa League and Conference League, where mismatches and heavily rotated line-ups create value in goals, handicap and double chance markets. We also cover Thursday fixtures in Asia and the Americas.'],
            'football-predictions-for-friday' => ['Friday', 'Friday night football opens the weekend in the Bundesliga, Ligue 1, Serie A, LaLiga and the Championship. With fewer games than Saturday, we can dig deeper into form, motivation and head-to-head trends for every match.'],
            'football-predictions-for-saturday' => ['Saturday', 'Saturday is the biggest day of the football week — the Premier League, LaLiga, Serie A, Bundesliga and Ligue 1 all play alongside hundreds of lower-league fixtures. Our Saturday list highlights the strongest selections across every market.'],
            'football-predictions-for-sunday' => ['Sunday', 'Sunday features headline games in Europe’s top leagues plus fixtures across Africa, Asia and the Americas. We review Saturday’s results and team news before publishing, so our Sunday tips reflect the very latest information.'],
            'football-predictions-for-the-weekend' => ['Weekend', 'The weekend is when most of the world’s football is played, from Friday night openers to late Sunday kick-offs. This page brings our best weekend selections together so you can plan your bets for Saturday and Sunday in one place.'],
        ];
    }

    /** @return array<string, string> */
    private function page(string $label, string $focus): array
    {
        $site = self::SITE;
        $when = $label === 'Weekend' ? 'this weekend' : 'on '.$label;
        $lower = strtolower($label);

        return [
            'title' => "{$label} Football Predictions & Betting Tips — {$site}",
            'head1' => "{$label} Football Predictions",
            'head2' => "Free expert football tips {$when}, researched and updated by the {$site} team.",
            'head3' => "{$label} Prediction",
            'meta_description' => "Free {$lower} football predictions and betting tips from {$site}: over/under goals, BTTS, double chance, home win and banker picks for every {$lower} fixture.",
            'meta_keywords' => "{$lower} football predictions, {$lower} betting tips, football tips {$lower}, sure predictions {$lower}, {$site}",
            'content' => <<<HTML
<h2>{$label} football predictions from {$site}</h2>
<p>{$focus}</p>
<p>Every prediction on this page is published free and covers the most popular markets: home win, away win, double chance, over 1.5 and 2.5 goals, both teams to score and our banker of the day. Each tip shows the odds we expect and is marked won or lost once the match ends, so you can always see how we are performing.</p>
<h3>How we build our {$lower} tips</h3>
<ul>
<li><strong>Form and fixtures</strong> — recent results weighed against the strength of the opposition.</li>
<li><strong>Team news</strong> — injuries, suspensions and likely rotation before we publish.</li>
<li><strong>Goals data</strong> — scoring and conceding rates home and away to guide goals markets.</li>
<li><strong>Price check</strong> — we only recommend selections where the odds offer fair value.</li>
</ul>
<h3>Want stronger picks?</h3>
<p>Our VIP plans include higher-confidence selections sent every day. Visit the <a href="/pricing">pricing page</a> to compare packages.</p>
<p><strong>Bet responsibly.</strong> Predictions are research, not guarantees. Only stake what you can afford to lose. 18+ only.</p>
HTML,
        ];
    }
}
