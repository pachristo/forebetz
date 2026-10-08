<?php

namespace Database\Seeders;

use App\Models\GameCat;
use App\Models\HomepageFaq;
use Illuminate\Database\Seeder;

/**
 * Pushes the Dailysuretips homepage hero, SEO and on-page content into the
 * `homeslug` tip category, plus the homepage FAQs. Safe to re-run.
 */
class HomepageContentSeeder extends Seeder
{
    public function run(): void
    {
        $home = GameCat::query()->where('slug', 'homeslug')->firstOrFail();

        $home->update([
            'title' => 'Dailysuretips — Football Predictions & Betting Tips',
            'meta_description' => 'Dailysuretips offers best football predictions and soccer betting tips in markets like over 2.5 goals, double chance, BTTS/GG, correct score, draw and banker tips today.',
            'meta_keywords' => 'football predictions, sure football predictions, free betting tips, soccer predictions today, correct score predictions, banker tips, dailysuretips',
            'head1' => 'Dailysuretips',
            'head2' => 'Prediction Site in the World',
            'descc' => 'Dailysuretips offers best football predictions and soccer betting tips in different betting markets like; over 2.5 goals, double chance, BTTS/GG, correct score, draw, and banker tips predictions today.',
            'fixture_heading' => 'Free Football Predictions',
            'content' => <<<'HTML'
<h2>Which Site Gives Sure Football Predictions?</h2>
<p>Looking for reliable football predictions? Dailysuretips is built for bettors who want clear tips across 1X2, over/under, BTTS, double chance, and correct score markets. Our analysts review form, head-to-head data, injuries, and odds movement to publish daily free predictions and premium packages.</p>
<p>Whether you follow the Premier League, La Liga, Serie A, Bundesliga, or Champions League, Dailysuretips brings fixtures, odds, and tip selections into one dark-themed prediction hub designed for speed and clarity.</p>
<h3>Why Choose Dailysuretips Prediction Site?</h3>
<p>We combine free daily tips with optional VIP access for banker tips and correct score selections. Track recent winnings, explore league tables, and follow sports articles that explain the reasoning behind big-match picks.</p>
<h3>Conclusion</h3>
<p>Dailysuretips remains focused on transparent tips, useful match data, and a clean experience for football fans. Join WhatsApp or Telegram, review today’s free predictions, and subscribe when you want deeper premium coverage.</p>
HTML,
        ]);

        $faqs = [
            ['Which site gives sure football predictions?', 'Dailysuretips provides researched football predictions across major leagues, combining form analysis, stats, and expert tips to help bettors make informed decisions.'],
            ['Are Dailysuretips predictions free?', 'Yes. Free Football Predictions are available daily with Yesterday, Today, and Tomorrow fixtures. Premium packages unlock deeper markets and correct score tips.'],
            ['How accurate are the predictions?', 'No tipster can guarantee 100% accuracy. Dailysuretips focuses on strong probabilities, recent form, and transparent recent winnings so you can track performance.'],
            ['What markets do you cover?', 'We cover 1X2, double chance, over/under 2.5, BTTS/GG, correct score, draw, and banker tips across top European and international leagues.'],
        ];

        foreach ($faqs as $i => [$question, $answer]) {
            HomepageFaq::query()->updateOrCreate(
                ['question' => $question],
                ['answer' => $answer, 'sort_order' => $i + 1, 'is_active' => true],
            );
        }
    }
}
