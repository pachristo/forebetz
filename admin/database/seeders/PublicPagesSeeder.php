<?php

namespace Database\Seeders;

use App\Models\SeoPage;
use Illuminate\Database\Seeder;

/**
 * Company and legal pages for the public site (Admin → Legal / site pages).
 * Safe to re-run: rows are matched by slug and refreshed.
 */
class PublicPagesSeeder extends Seeder
{
    private const SITE = 'Dailysuretips';

    private const EMAIL = 'Forebetz@gmail.com';

    public function run(): void
    {
        foreach ($this->pages() as $slug => $page) {
            SeoPage::query()->updateOrCreate(['slug' => $slug], $page + [
                'category' => 'Legal / site page',
                'status' => 'published',
                'date' => now(),
            ]);
        }
    }

    /** @return array<string, array<string, string>> */
    private function pages(): array
    {
        $site = self::SITE;
        $email = self::EMAIL;

        return [
            'about-us' => [
                'title' => "About Us — {$site} Football Predictions",
                'head1' => 'About Us',
                'head2' => 'Who we are and what we offer',
                'meta_description' => "{$site} is a football prediction platform run by analysts and data lovers, publishing free daily predictions and premium VIP tips.",
                'meta_keywords' => 'about dailysuretips, football prediction site, free football predictions, betting tips team',
                'content' => <<<HTML
<h2>Who We Are</h2>
<p>{$site} is a football prediction platform created for one purpose: to help bettors and football lovers make smarter decisions every day. We are a dedicated team of football analysts, data lovers and betting strategists who study the game so you don&rsquo;t have to guess.</p>
<p>New prediction sites appear every week, yet finding accurate, honest football tips is still hard. Too many bettors rely on guesswork. We built {$site} to change that with transparent, data-led predictions and a public record of our results.</p>
<h2>What We Offer</h2>
<p>We publish <strong>free football predictions every day</strong>. Our free tips are not guesswork; they come from match statistics, current team form, goal averages, head-to-head records and betting market trends.</p>
<p>Every day you will find predictions in markets such as:</p>
<ol>
<li>Home, draw and away (1X2)</li>
<li>Double chance</li>
<li>Over and under goals (1.5, 2.5 and 3.5)</li>
<li>Both teams to score (GG/NG)</li>
<li>Draw no bet and win either half</li>
<li>Correct score</li>
<li>Banker of the day</li>
</ol>
<p>We cover the biggest leagues in the world, including the Premier League, La Liga, Serie A, the Bundesliga and Ligue 1, as well as lesser-known leagues that often offer better value.</p>
<h2>VIP Packages</h2>
<p>For members who want more, our <a href="/pricing">VIP packages</a> unlock premium daily selections with carefully filtered odds. VIP tips appear on your dashboard as soon as your subscription is active.</p>
<h2>Bet Responsibly</h2>
<p>No prediction is ever guaranteed. Please only bet with money you can afford to lose and only if you are 18 or older.</p>
HTML,
            ],

            'disclaimer' => [
                'title' => "Disclaimer — {$site}",
                'head1' => 'Disclaimer',
                'head2' => 'Legal terms',
                'meta_description' => "{$site} provides sports predictions for informational purposes only. Read our full disclaimer before using the site.",
                'meta_keywords' => 'dailysuretips disclaimer, betting disclaimer, football predictions disclaimer',
                'content' => <<<HTML
<p>{$site} is an informational platform that provides sports predictions and analysis. We are not a bookmaker and we do not accept or place bets on behalf of users. All predictions and analysis on this site are for informational purposes only and are not financial or betting advice.</p>
<p>Our predictions, strategies and recommendations are based on statistical analysis, historical data and expert opinion. They can be wrong and should be treated as guidance, not guarantees. Final betting decisions rest solely with you, and {$site} cannot be held responsible for any losses incurred from using our information.</p>
<p>Sports betting may be illegal in some jurisdictions. It is your responsibility to make sure your activities comply with the laws where you live. {$site} makes no representation about the legality of online gambling in any jurisdiction.</p>
<h2>By using {$site}, you agree that:</h2>
<ul>
<li>You are at least 18 years old, or the legal gambling age in your jurisdiction.</li>
<li>You use the information provided at your own risk.</li>
<li>{$site} and its affiliates are not responsible for any financial loss or damage.</li>
<li>You are responsible for verifying information before making any decision.</li>
</ul>
<p>We may update this disclaimer at any time without prior notice. If you believe any content on this site infringes your copyright, please contact us at <a href="mailto:{$email}">{$email}</a> and we will resolve it promptly.</p>
<p><strong>Gamble responsibly.</strong> Only bet with money you can afford to lose. If you think you may have a gambling problem, please seek help from a professional organisation in your area.</p>
HTML,
            ],

            'refund-policy' => [
                'title' => "Refund Policy — {$site}",
                'head1' => 'Refund Policy',
                'head2' => 'Legal terms',
                'meta_description' => "Read the {$site} refund policy for VIP prediction packages, including the limited cases where refunds or credits are available.",
                'meta_keywords' => 'dailysuretips refund policy, vip package refund, prediction subscription refund',
                'content' => <<<HTML
<h2>1. Our Commitment</h2>
<p>At {$site} we are dedicated to providing well-researched football predictions. Because our services are digital and sports results are unpredictable, we operate a strict no-refund policy for prediction packages, with the limited exceptions below.</p>
<h2>2. No Refund Policy</h2>
<p>All purchases of VIP packages are final. We do not provide refunds for:</p>
<ul>
<li>Prediction accuracy or performance</li>
<li>Changes in betting odds or match outcomes</li>
<li>Personal circumstances or change of mind</li>
<li>Inability to place bets with a bookmaker</li>
<li>Service suspension due to a breach of our terms</li>
</ul>
<h2>3. Service Guarantee</h2>
<p>We work hard to be accurate, but we cannot guarantee betting results. Our predictions are based on careful analysis and remain subject to the unpredictable nature of sport.</p>
<h2>4. Exceptional Circumstances</h2>
<p>We will only consider a refund request for:</p>
<ul>
<li>Duplicate charges for the same subscription</li>
<li>Technical problems that prevent access to your VIP tips for more than 48 hours</li>
<li>Unauthorised transactions (verification required)</li>
</ul>
<h2>5. Service Credits</h2>
<p>Where a service disruption is verified, we may extend your subscription instead of issuing a refund.</p>
<h2>6. How to Contact Us</h2>
<p>For questions about this policy, email <a href="mailto:{$email}">{$email}</a>. Our support team is available Monday to Friday, 9:00 AM to 5:00 PM GMT.</p>
<h2>7. Policy Updates</h2>
<p>We may change this refund policy at any time. Changes take effect as soon as they are posted, and continued use of our services means you accept the updated policy.</p>
HTML,
            ],

            'terms-and-condition' => [
                'title' => "Terms & Conditions — {$site}",
                'head1' => 'Terms & Conditions',
                'head2' => 'Legal terms',
                'meta_description' => "The terms and conditions for using {$site}, including predictions, VIP packages, external links and responsible gambling.",
                'meta_keywords' => 'dailysuretips terms and conditions, terms of use, vip packages terms',
                'content' => <<<HTML
<p>Welcome to {$site}. These terms explain how you may use this website (&ldquo;the Site&rdquo;, &ldquo;we&rdquo;, &ldquo;us&rdquo;, &ldquo;our&rdquo;). If you do not agree with any part of them, please stop using the Site. Using the Site means you understand and accept these terms.</p>
<h2>Your Acceptance of These Terms</h2>
<p>By using the Site you agree to these terms and to our <a href="/disclaimer">Disclaimer</a> and <a href="/privacy">Privacy Policy</a>. We may update these terms as the platform grows; changes take effect as soon as they are posted, so please check this page regularly.</p>
<h2>Predictions Are for Information Only</h2>
<p>Predictions on {$site} are not financial or investment advice. What you choose to do with them is your responsibility. By using the Site you agree that:</p>
<ul>
<li>We do not guarantee that any prediction, tip or advice will be correct or profitable.</li>
<li>Any action you take based on our content is entirely at your own risk.</li>
<li>Decisions to place bets or spend money are yours alone.</li>
<li>We are not liable for any loss, damage or expense arising from use of the Site.</li>
</ul>
<h2>Accounts</h2>
<p>You must be 18 or older to create an account. Keep your login details private; you are responsible for activity on your account. We may suspend accounts that share VIP content, abuse the service or break these terms.</p>
<h2>Pricing and VIP Packages</h2>
<p>{$site} offers free predictions and paid VIP packages. Each package is clearly priced on our <a href="/pricing">pricing page</a>, and payment is required before VIP access is activated. Only pay through the methods shown on our official website. If anyone claims to represent {$site} and asks for payment elsewhere, please report them to us.</p>
<h2>External Links and Bookmakers</h2>
<p>The Site may link to third-party websites, including bookmakers and partners. We do not control those sites and are not responsible for their content, offers or practices. Check the terms of any bookmaker before you open an account.</p>
<h2>Contact</h2>
<p>Questions about these terms? Email <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
            ],

            'policy' => [
                'title' => "Privacy Policy — {$site}",
                'head1' => 'Privacy Policy',
                'head2' => 'Legal terms',
                'meta_description' => "How {$site} collects, uses and protects your personal information, cookies and account data.",
                'meta_keywords' => 'dailysuretips privacy policy, cookies, personal data',
                'content' => <<<HTML
<p>This Privacy Policy explains how {$site} collects, uses, protects and discloses information when you use our website and services. By continuing to use the Site you consent to the practices described here.</p>
<h2>Information We Collect</h2>
<h3>Personal information</h3>
<p>We collect personal information only when you give it to us, for example when you create an account, subscribe to a VIP package, fill in our contact form or email us. This may include:</p>
<ul>
<li>Your name and username</li>
<li>Your email address and phone number</li>
<li>Your country</li>
<li>Your subscription history</li>
<li>Any message you send us</li>
</ul>
<p>Your account password is stored in encrypted (hashed) form and is never visible to our team. We do not store your bank card details.</p>
<h3>Non-personal information</h3>
<p>When you visit the Site and accept cookies, we may collect your IP address, browser and device type, the pages you visit and how long you stay. We use this to understand how the Site is used and to improve it.</p>
<h2>How We Use Your Information</h2>
<ul>
<li>To provide your account and VIP access</li>
<li>To answer your questions and support requests</li>
<li>To send service updates and, if you opt in, newsletters</li>
<li>To improve the Site and understand visitor behaviour</li>
<li>To protect you and us from fraud and abuse</li>
</ul>
<p>We never sell your personal data.</p>
<h2>Cookies</h2>
<p>We use essential cookies to keep you logged in, performance cookies for analytics, and affiliate cookies to know when a partner link is clicked. You can disable cookies in your browser, but some features may stop working.</p>
<h2>Third-Party Tools</h2>
<p>We use Google Analytics and similar tools to measure traffic. These providers process data under their own privacy policies.</p>
<h2>Your Rights</h2>
<p>You can update your details on your <a href="/profile">account page</a>, or email <a href="mailto:{$email}">{$email}</a> to request a copy or deletion of your data.</p>
HTML,
            ],

            'contact' => [
                'title' => "Contact Us — {$site}",
                'head1' => 'Contact Us',
                'head2' => 'Questions about predictions, VIP packages or payments? We are here to help.',
                'meta_description' => "Contact the {$site} team by email or WhatsApp for help with predictions, VIP packages and payments.",
                'meta_keywords' => 'contact dailysuretips, dailysuretips support, vip payment help',
                'content' => <<<HTML
<p>Our support team usually replies within 24 hours, Monday to Friday. For VIP payment confirmations, WhatsApp is the fastest way to reach us.</p>
HTML,
            ],

            'partners' => [
                'title' => "Our Partners — {$site}",
                'head1' => 'Our Partners',
                'head2' => 'Trusted sites we work with',
                'meta_description' => "Trusted football prediction and betting partners of {$site}.",
                'meta_keywords' => 'dailysuretips partners, betting partners, football tips partners',
                'content' => '',
            ],
        ];
    }
}
