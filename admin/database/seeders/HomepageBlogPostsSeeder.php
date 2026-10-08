<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Publishes the three launch articles shown in the homepage "Sports Articles" block.
 * Images live in storage/app/public/blog-images. Safe to re-run (matched by slug).
 */
class HomepageBlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->orderBy('id')->value('id');

        $categories = [
            'predictions' => BlogCategory::query()->updateOrCreate(
                ['slug' => 'predictions'],
                ['name' => 'Predictions', 'description' => 'Match previews and football predictions from the Dailysuretips team.'],
            ),
            'betting-guides' => BlogCategory::query()->updateOrCreate(
                ['slug' => 'betting-guides'],
                ['name' => 'Betting Guides', 'description' => 'Practical guides to betting markets, value and bankroll management.'],
            ),
        ];

        foreach ($this->posts() as $post) {
            $category = $categories[$post['category']];

            Blog::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'creator' => $creator,
                    'title' => $post['title'],
                    'category' => $category->name,
                    'blog_category_id' => $category->id,
                    'content' => trim($post['content']),
                    'display_image' => $post['image'],
                    'status' => 'published',
                    'date' => Carbon::parse($post['date']),
                    'likes' => 0,
                    'meta_keywords' => $post['keywords'],
                    'meta_description' => $post['description'],
                    'other' => ['source' => 'homepage-launch-articles'],
                ],
            );
        }
    }

    /** @return list<array<string, string>> */
    private function posts(): array
    {
        return [
            [
                'slug' => 'best-premier-league-predictions-this-weekend',
                'title' => 'Best Premier League Predictions for This Weekend',
                'category' => 'predictions',
                'image' => 'blog-images/premier-league-weekend-predictions.webp',
                'date' => '2026-10-08',
                'keywords' => 'premier league predictions, epl tips this weekend, premier league betting tips, football predictions today',
                'description' => 'How we build our Premier League predictions every weekend — form, home advantage, goals data and team news — plus the markets that offer the best value.',
                'content' => <<<'HTML'
<p>The Premier League is the most-watched and most-bet league in the world, which also makes it one of the hardest to beat. Prices are sharp, information spreads fast and a single team-news update can move the market in minutes. This guide explains how the Dailysuretips team approaches a Premier League weekend and which markets we lean on when building our free predictions.</p>

<h2>1. Start with form — but read it properly</h2>
<p>A run of five wins looks impressive, but the quality of those opponents matters more than the results themselves. Before backing a side we check:</p>
<ul>
<li><strong>Opposition strength</strong> — three wins against bottom-half teams are not the same as three wins against the top six.</li>
<li><strong>Underlying numbers</strong> — shots, shots on target and big chances created tell you whether results are sustainable or lucky.</li>
<li><strong>Home and away splits</strong> — many mid-table sides are a completely different team on the road.</li>
</ul>

<h2>2. Team news changes everything</h2>
<p>Injuries, suspensions and rotation are the biggest reason predictions fail. Fixtures that fall between European nights are especially risky because managers often rest key players. We publish our final picks as close to kick-off as practical so confirmed line-ups can be factored in, and we always recommend checking the official team sheets yourself before placing a bet.</p>

<h2>3. Markets that work well in the Premier League</h2>
<h3>Double chance (1X / X2)</h3>
<p>When a strong home side faces a disciplined defensive team, the straight home win is often too short to be worth it. Double chance trades some price for a lot of safety and suits accumulator builders.</p>
<h3>Over 1.5 and Over 2.5 goals</h3>
<p>Top-flight matches between attacking sides regularly clear 2.5 goals, but low-block games involving relegation-threatened teams often do not. We only recommend overs when both sides create chances <em>and</em> concede them.</p>
<h3>Both teams to score (GG / NG)</h3>
<p>BTTS is strongest when two attacking teams with leaky defences meet. If one side has kept several clean sheets in a row, NG (no goal) is frequently the value side.</p>

<h2>4. Avoid the classic traps</h2>
<ul>
<li><strong>Backing big names by reflex</strong> — reputation is already priced in.</li>
<li><strong>Over-sized accumulators</strong> — every extra leg multiplies the risk. Two to four well-researched selections beat a ten-fold lottery ticket.</li>
<li><strong>Chasing losses</strong> — a bad Saturday does not need fixing on Sunday.</li>
</ul>

<h2>Where to find this weekend's picks</h2>
<p>Our free football predictions are published daily on the homepage, with Yesterday, Today and Tomorrow tabs so you can review results and plan ahead. For specific markets, browse the <a href="/category?cat=double-chance-predictions">double chance</a> and <a href="/category?cat=over-1-5-goals-predictions">over 1.5 goals</a> categories.</p>

<p><strong>Bet responsibly.</strong> Predictions are research, not guarantees. Only stake what you can afford to lose, and take a break if betting stops being fun. 18+ only.</p>
HTML,
            ],
            [
                'slug' => 'how-to-find-value-bets-with-correct-score-tips',
                'title' => 'How to Find Value Bets with Correct Score Tips',
                'category' => 'betting-guides',
                'image' => 'blog-images/correct-score-value-bets.webp',
                'date' => '2026-10-07',
                'keywords' => 'correct score tips, correct score predictions, value bets football, how to bet correct score',
                'description' => 'Correct score betting offers big odds but low strike rates. Learn how to pick realistic scorelines, spot value and manage stakes the smart way.',
                'content' => <<<'HTML'
<p>Correct score is one of the most tempting markets in football betting. Odds of 6.00 to 12.00 are normal, and even a small stake can return a big profit. The catch is obvious: you are trying to predict the exact final result, so you will lose far more often than you win. The goal is not to win every bet — it is to make sure the prices you take are bigger than the real chance of that score happening.</p>

<h2>What “value” means in correct score betting</h2>
<p>A bet has value when the bookmaker's odds imply a lower probability than the true chance of the outcome. For example, odds of 8.00 imply a 12.5% chance. If your research suggests a 1-0 home win happens closer to 15% of the time in that type of fixture, the bet has value even though it will still lose most of the time.</p>

<h2>Step 1: Estimate expected goals for each team</h2>
<p>Start with how many goals each team typically scores and concedes, adjusted for home and away form and the strength of the opponent. A side averaging around 1.6 goals at home against a defence conceding 1.2 away gives you a sensible starting point for the home team's goal expectation.</p>

<h2>Step 2: Focus on the most common scorelines</h2>
<p>Across major leagues, a small group of scorelines appears again and again. The most frequent results are usually:</p>
<ul>
<li><strong>1-0 and 0-1</strong> — common in tight, low-scoring fixtures.</li>
<li><strong>1-1</strong> — the most frequent draw in most leagues.</li>
<li><strong>2-1 and 1-2</strong> — typical when both teams score but one edges it.</li>
<li><strong>2-0 and 0-2</strong> — favourites controlling a game against weaker opposition.</li>
</ul>
<p>Exotic scores such as 4-3 make headlines, but they are rarely good value because they happen so infrequently.</p>

<h2>Step 3: Match the score to the game script</h2>
<p>Ask how the match is likely to play out. A dominant favourite against a team that defends deep points towards 2-0 or 1-0. Two attacking sides with poor defences point towards 2-1, 2-2 or 1-1. Our correct score tips combine these game scripts with recent form and head-to-head patterns.</p>

<h2>Step 4: Cover more than one score — carefully</h2>
<p>Many experienced bettors split one stake across two or three related scores, for example 1-0, 2-0 and 2-1 for a home favourite. This lowers the payout but improves the strike rate. Always check that the combined odds still offer a worthwhile return.</p>

<h2>Step 5: Keep stakes small and consistent</h2>
<p>Because losing runs are normal in this market, correct score bets should be a small part of your bankroll. A fixed small stake per tip protects you from the inevitable dry spells and lets the bigger odds work in your favour over time.</p>

<h2>Where to find our correct score tips</h2>
<p>Dailysuretips publishes correct score predictions as part of our premium packages, alongside free daily picks in safer markets on the homepage. Track how our tips perform in the Recent Winnings section before you commit.</p>

<p><strong>Bet responsibly.</strong> Correct score is a high-variance market. Never stake money you cannot afford to lose. 18+ only.</p>
HTML,
            ],
            [
                'slug' => 'champions-league-banker-tips-and-analysis',
                'title' => 'Champions League Banker Tips and Analysis',
                'category' => 'predictions',
                'image' => 'blog-images/champions-league-banker-tips.webp',
                'date' => '2026-10-06',
                'keywords' => 'champions league banker tips, ucl predictions, banker of the day, champions league betting tips',
                'description' => 'What makes a reliable Champions League banker? We break down the factors we use to pick our safest UCL selections and the mistakes to avoid.',
                'content' => <<<'HTML'
<p>A “banker” is the selection you trust most — the bet you build an accumulator around or back as a confident single. In the Champions League, finding a true banker is harder than it looks. Every team has earned its place, motivation is high and even heavy favourites can be frustrated away from home. Here is how we approach our Champions League banker tips.</p>

<h2>What makes a good banker?</h2>
<p>A banker is not simply the shortest price on the coupon. Our banker selections usually tick most of these boxes:</p>
<ul>
<li><strong>A clear quality gap</strong> between the two squads, not just a difference in reputation.</li>
<li><strong>Strong home form</strong> in Europe, where crowds and familiarity make a real difference.</li>
<li><strong>Motivation</strong> — a side that needs points to progress is more reliable than one already qualified and rotating.</li>
<li><strong>Stable team news</strong> with key attackers and the first-choice defence available.</li>
</ul>

<h2>Group and league-phase dynamics</h2>
<p>Context matters enormously in European competition. Early matchdays are often cagey as teams avoid defeat. Later rounds become predictable once some sides have little to play for, which can create excellent banker opportunities against weakened line-ups — and traps for anyone backing a favourite that has already qualified.</p>

<h2>Markets we prefer for bankers</h2>
<h3>Home win or double chance</h3>
<p>For elite home sides facing much weaker opposition, the home win can be a solid banker. Where the gap is smaller, double chance (1X) keeps the selection safe while still adding value to an accumulator.</p>
<h3>Over 1.5 goals</h3>
<p>Champions League matches involving top attacking sides frequently produce at least two goals. Over 1.5 is a popular banker market because it does not depend on who wins.</p>
<h3>Draw no bet</h3>
<p>Draw no bet returns your stake if the match ends level — a useful safety net for away favourites in tricky venues.</p>

<h2>Common mistakes with Champions League bankers</h2>
<ul>
<li><strong>Ignoring the weekend schedule</strong> — big league matches before or after European nights often lead to rotation.</li>
<li><strong>Trusting away favourites too much</strong> — long travel and hostile atmospheres level the playing field.</li>
<li><strong>Stacking too many “sure” picks</strong> — five safe-looking selections combined are no longer safe.</li>
</ul>

<h2>Get our banker of the day</h2>
<p>Our team publishes a daily banker selection across all major competitions, including the Champions League. Follow the <a href="/category?cat=sure-banker-of-the-day">Banker Tips</a> page and join our Telegram or WhatsApp channels for updates as team news is confirmed.</p>

<p><strong>Bet responsibly.</strong> No bet is guaranteed, however safe it looks. Set limits, stake sensibly and never chase losses. 18+ only.</p>
HTML,
            ],
        ];
    }
}
