<h1>Dailysuretips Admin Guide</h1>
  <p class="sub"><strong>How to access the backend</strong> and where SEO header content is formed for each page type</p>

  <h2>1. How to access the little backend (admin)</h2>
  <div class="box">
    <strong>Backend (admin) URL:</strong> open in a browser:<br>
    <span class="mono">{{ url("/admin") }}</span><br><br>
    <strong>Public website:</strong> <span class="mono">https://dailysuretips.com</span><br><br>
    Log in with the admin email and password you were given. After login you land on the Dashboard.<br>
    All SEO edits are under the left menu group <strong>Content Management</strong>.
  </div>
  <p>Direct links (bookmark these):</p>
  <table>
    <thead>
      <tr>
        <th>What you want to edit</th>
        <th>Left menu in backend</th>
        <th>Direct backend URL</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Homepage SEO + tip category SEO</td>
        <td>Content Management → <strong>Tip categories</strong></td>
        <td><span class="mono">{{ url("/admin/game-cats") }}</span></td>
      </tr>
      <tr>
        <td>About, Privacy, Terms, Contact, Blog index, etc.</td>
        <td>Content Management → <strong>Legal &amp; site pages</strong></td>
        <td><span class="mono">{{ url("/admin/site-legal-pages") }}</span></td>
      </tr>
      <tr>
        <td>Custom SEO landing pages</td>
        <td>Content Management → <strong>Other SEO pages</strong></td>
        <td><span class="mono">{{ url("/admin/seo-pages") }}</span></td>
      </tr>
    </tbody>
  </table>
  <p>On each edit form, scroll to the section titled <strong>SEO</strong>. That is where the header meta is stored:</p>
  <ul class="compact">
    <li><strong>Title</strong> → becomes the browser <span class="mono">&lt;title&gt;</span> and <span class="mono">og:title</span></li>
    <li><strong>Meta description</strong> → becomes <span class="mono">&lt;meta name="description"&gt;</span> and <span class="mono">og:description</span></li>
    <li><strong>Meta keywords</strong> → becomes <span class="mono">&lt;meta name="keywords"&gt;</span></li>
    <li><strong>Head 1 / Head 2</strong> → on-page headlines; Head 2 is also used as a fallback description if Meta description is empty</li>
  </ul>

  <h2>2. How the SEO header is formed on the front (important)</h2>
  <p>When a visitor opens a public page, the front site builds the SEO header in this order. The first non-empty value wins:</p>
  <table>
    <thead>
      <tr>
        <th>Page</th>
        <th>Where the header content is taken from</th>
        <th>How the description is formed</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Homepage</strong></td>
        <td>Backend → Tip categories → slug <span class="mono">homeslug</span></td>
        <td>1) Meta description<br>2) if empty → Head 2<br>3) if empty → first ~160 characters of Content<br>4) if still empty → default: “Dailysuretips offers free football predictions…”</td>
      </tr>
      <tr>
        <td><strong>Tip categories</strong></td>
        <td>Backend → Tip categories → that category row</td>
        <td>Same as homepage: Meta description → Head 2 → Content snippet</td>
      </tr>
      <tr>
        <td><strong>Static / legal pages</strong></td>
        <td>Backend → Legal &amp; site pages → that page</td>
        <td>1) Meta description<br>2) if empty → Head 2<br>3) if empty → Content snippet</td>
      </tr>
      <tr>
        <td><strong>Other SEO pages</strong></td>
        <td>Backend → Other SEO pages → that page</td>
        <td>Same as legal pages</td>
      </tr>
      <tr>
        <td><strong>Match pages</strong></td>
        <td>Not in backend. Built automatically from team names.</td>
        <td><em>{Home} vs {Away} prediction, H2H, statistics and betting tips on Dailysuretips.</em></td>
      </tr>
      <tr>
        <td><strong>League pages</strong></td>
        <td>Not in backend. Built automatically from league + country names.</td>
        <td><em>Dailysuretips {League} predictions, upcoming matches, and expert tips for {Country}.</em></td>
      </tr>
    </tbody>
  </table>
  <div class="note">
    So if Meta description is blank, the site still shows a description (from Head 2 or Content). To fully control Google’s snippet, always fill <strong>Meta description</strong> yourself.
  </div>

  <h2>3. Developer map — Blade &amp; PHP files that handle SEO</h2>
  <p>Use this when you need to change wording that is <strong>not</strong> editable in admin (match/league templates), or to find where a CMS field becomes a meta tag.</p>

  <h3>Where the code lives — full directories</h3>
  <div class="box">
    <strong>Production server</strong> (live site)<br>
    Host: <span class="mono">66.29.138.42</span> · SSH user: <span class="mono">root</span><br><br>
    <strong>Front (public site)</strong> → <span class="mono">/var/www/winning/front/</span><br>
    <strong>Admin (this backend)</strong> → <span class="mono">/var/www/winning/admin/</span>
  </div>
  <div class="box">
    <strong>Local dev copy</strong> (on your machine / docker setup)<br>
    Front: <span class="mono">sites/forebetz/front/</span><br>
    Admin: <span class="mono">sites/forebetz/admin/</span><br>
    <em>(Full path example: <span class="mono">…/nginx_docker/sites/forebetz/front/</span>)</em>
  </div>
  <p><strong>Folder layout</strong> — start here, then drill into the paths in the tables below:</p>
  @verbatim
  <pre class="mono" style="white-space:pre-wrap;background:#f4f7f5;padding:10px 12px;border-radius:6px;font-size:.82rem;overflow:auto;">/var/www/winning/
├── front/                              ← dailysuretips.com (public)
│   ├── app/Livewire/                   ← each page class; passes meta to layout
│   ├── app/Services/                   ← builds title/description from DB
│   ├── resources/views/layouts/        ← &lt;head&gt; tags (title, meta, og:*)
│   ├── resources/views/livewire/       ← visible page HTML
│   ├── resources/views/home/           ← shared header/nav (not &lt;head&gt;)
│   └── routes/web.php                  ← which URL → which Livewire page
└── admin/                              ← admin.dailysuretips.com
    ├── app/Filament/Resources/         ← CMS edit forms (SEO section)
    └── resources/views/filament/       ← admin-only pages (e.g. this guide)</pre>
  @endverbatim
  <p><strong>How to open a file on the server (SSH):</strong></p>
  @verbatim
  <pre class="mono" style="white-space:pre-wrap;background:#f4f7f5;padding:10px 12px;border-radius:6px;font-size:.82rem;overflow:auto;">ssh root@66.29.138.42
cd /var/www/winning/front
ls resources/views/layouts/
nano resources/views/layouts/app.blade.php    # edit layout meta tags
nano app/Services/MatchPageData.php           # edit auto match description</pre>
  @endverbatim
  <p>Or use an SFTP client (FileZilla, Cyberduck): connect to the same host, then browse to <span class="mono">/var/www/winning/front/</span> or <span class="mono">/var/www/winning/admin/</span>.</p>

  <p><strong>Key files — full paths on production:</strong></p>
  <table>
    <thead>
      <tr>
        <th>What it does</th>
        <th>Full path on server</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Prints &lt;title&gt; + meta description in &lt;head&gt;</td>
        <td><span class="mono">/var/www/winning/front/resources/views/layouts/app.blade.php</span></td>
      </tr>
      <tr>
        <td>Homepage SEO from <span class="mono">homeslug</span></td>
        <td><span class="mono">/var/www/winning/front/app/Livewire/HomePage.php</span><br>
            <span class="mono">/var/www/winning/front/app/Services/HomePageData.php</span></td>
      </tr>
      <tr>
        <td>Tip category pages</td>
        <td><span class="mono">/var/www/winning/front/app/Livewire/TipCategoryPage.php</span><br>
            <span class="mono">/var/www/winning/front/resources/views/livewire/tip-category-page.blade.php</span></td>
      </tr>
      <tr>
        <td>Legal / static pages (about, privacy…)</td>
        <td><span class="mono">/var/www/winning/front/app/Livewire/PublicSlugPage.php</span><br>
            <span class="mono">/var/www/winning/front/app/Services/StaticPageData.php</span><br>
            <span class="mono">/var/www/winning/front/resources/views/livewire/static-page.blade.php</span></td>
      </tr>
      <tr>
        <td>Match page auto description</td>
        <td><span class="mono">/var/www/winning/front/app/Services/MatchPageData.php</span><br>
            <span class="mono">/var/www/winning/front/app/Livewire/MatchPage.php</span></td>
      </tr>
      <tr>
        <td>League page auto description</td>
        <td><span class="mono">/var/www/winning/front/app/Livewire/LeaguePage.php</span><br>
            <span class="mono">/var/www/winning/front/app/Services/HomePageData.php</span></td>
      </tr>
      <tr>
        <td>URL routing (which page loads)</td>
        <td><span class="mono">/var/www/winning/front/routes/web.php</span></td>
      </tr>
      <tr>
        <td>Admin: Tip categories SEO form</td>
        <td><span class="mono">/var/www/winning/admin/app/Filament/Resources/GameCatResource.php</span></td>
      </tr>
      <tr>
        <td>Admin: Legal &amp; site pages SEO form</td>
        <td><span class="mono">/var/www/winning/admin/app/Filament/Resources/SiteLegalPageResource.php</span></td>
      </tr>
      <tr>
        <td>Admin: Other SEO pages form</td>
        <td><span class="mono">/var/www/winning/admin/app/Filament/Resources/SeoPageResource.php</span></td>
      </tr>
      <tr>
        <td>This documentation page</td>
        <td><span class="mono">/var/www/winning/admin/resources/views/filament/pages/partials/seo-meta-guide-body.blade.php</span></td>
      </tr>
    </tbody>
  </table>
  <div class="note">
    Paths under <span class="mono">front/</span> affect the public website. Paths under <span class="mono">admin/</span> affect this backend only. After editing PHP/Blade on the server, run <span class="mono">php artisan view:clear</span> inside that app folder if changes do not show immediately.
  </div>

  <h3>A. Blade that prints the SEO header tags</h3>
  <p>Almost every public page uses this layout. Meta tags are rendered here from Livewire <span class="mono">layoutData</span>:</p>
  <p><span class="mono">front/resources/views/layouts/app.blade.php</span></p>
  @verbatim
  <pre class="mono" style="white-space:pre-wrap;background:#f4f7f5;padding:10px 12px;border-radius:6px;font-size:.82rem;overflow:auto;">@php
    $pageTitle = trim((string) ($title ?? config('app.name', 'Dailysuretips')));
    $pageDescription = trim((string) ($meta_description ?? 'Dailysuretips offers free football predictions…'));
@endphp
<title>{{ strip_tags($pageTitle) }}</title>
<meta name="description" content="{{ Str::limit(strip_tags($pageDescription), 320, '') }}">
<meta property="og:description" content="…">
<meta name="twitter:description" content="…"></pre>
  @endverbatim
  <p>Login / account shells (usually <span class="mono">noindex</span>):</p>
  <ul class="compact">
    <li><span class="mono">front/resources/views/layouts/auth.blade.php</span></li>
    <li><span class="mono">front/resources/views/layouts/account.blade.php</span></li>
  </ul>

  <h3>B. Page body Blades (on-page content, not the &lt;head&gt;)</h3>
  <p>These views render the visible page. SEO for Google is still passed via the Livewire PHP class → layout above.</p>
  <table>
    <thead>
      <tr>
        <th>Page type</th>
        <th>Blade view</th>
        <th>Livewire class (sets meta)</th>
        <th>PHP that builds the text</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Homepage</td>
        <td><span class="mono">resources/views/livewire/home-page.blade.php</span></td>
        <td><span class="mono">app/Livewire/HomePage.php</span></td>
        <td><span class="mono">app/Services/HomePageData.php</span> (from tip category slug <span class="mono">homeslug</span>)</td>
      </tr>
      <tr>
        <td>Tip categories</td>
        <td><span class="mono">livewire/tip-category-page.blade.php</span></td>
        <td><span class="mono">TipCategoryPage.php</span></td>
        <td><span class="mono">HomePageData.php</span> → category row in <span class="mono">game_cats</span></td>
      </tr>
      <tr>
        <td>Legal / static</td>
        <td><span class="mono">livewire/static-page.blade.php</span></td>
        <td><span class="mono">PublicSlugPage.php</span></td>
        <td><span class="mono">StaticPageData.php</span> → <span class="mono">seo_pages</span></td>
      </tr>
      <tr>
        <td>Other SEO landings</td>
        <td><span class="mono">livewire/home-page.blade.php</span> (reused shell)</td>
        <td><span class="mono">PublicSlugPage.php</span></td>
        <td><span class="mono">HomePageData.php</span> → <span class="mono">seo_pages</span></td>
      </tr>
      <tr>
        <td>Match pages</td>
        <td><span class="mono">livewire/match-page.blade.php</span></td>
        <td><span class="mono">MatchPage.php</span></td>
        <td><span class="mono">MatchPageData.php</span> — auto string near <span class="mono">pageDescription</span></td>
      </tr>
      <tr>
        <td>League pages</td>
        <td><span class="mono">livewire/league-page.blade.php</span></td>
        <td><span class="mono">LeaguePage.php</span></td>
        <td><span class="mono">HomePageData.php</span> → <span class="mono">forLeague()</span> (auto string)</td>
      </tr>
      <tr>
        <td>Blog index / post</td>
        <td><span class="mono">livewire/blog-index-page.blade.php</span>, <span class="mono">blog-post-page.blade.php</span></td>
        <td><span class="mono">BlogIndexPage.php</span>, <span class="mono">BlogPostPage.php</span></td>
        <td><span class="mono">BlogPageData.php</span></td>
      </tr>
      <tr>
        <td>Contact</td>
        <td><span class="mono">livewire/contact-page.blade.php</span></td>
        <td><span class="mono">ContactPage.php</span></td>
        <td>CMS override via <span class="mono">StaticPageData</span> when present</td>
      </tr>
    </tbody>
  </table>
  <div class="note">
    Shared chrome (header/nav) is often included from <span class="mono">resources/views/home/header.blade.php</span> — that file is for the hero UI, not the HTML <span class="mono">&lt;head&gt;</span> meta tags.
  </div>

  <h3>C. How Livewire hands meta to the layout</h3>
  <p>Example pattern (homepage). Look for <span class="mono">layoutData</span> and <span class="mono">meta_description</span> in each Livewire class:</p>
  @verbatim
  <pre class="mono" style="white-space:pre-wrap;background:#f4f7f5;padding:10px 12px;border-radius:6px;font-size:.82rem;overflow:auto;">// app/Livewire/HomePage.php
return view('livewire.home-page', $payload)
    ->title(...)
    ->layoutData([
        'meta_description' => (string) ($seo['description'] ?? $payload['pageDescription'] ?? ''),
        'meta_keywords' => ...,
        'canonical_url' => ...,
    ]);</pre>
  @endverbatim

  <h3>D. Auto-generated match description (template change)</h3>
  <p>File: <span class="mono">front/app/Services/MatchPageData.php</span></p>
  @verbatim
  <pre class="mono" style="white-space:pre-wrap;background:#f4f7f5;padding:10px 12px;border-radius:6px;font-size:.82rem;overflow:auto;">'pageDescription' => $homeName.' vs '.$awayName
    .' prediction, H2H, statistics and betting tips on Dailysuretips.',</pre>
  @endverbatim
  <p>Changing that string updates <strong>all</strong> match pages. There is no admin field for it.</p>

  <h3>E. Admin forms that store the CMS SEO fields</h3>
  <table>
    <thead>
      <tr><th>Admin screen</th><th>Filament resource file</th><th>DB table</th></tr>
    </thead>
    <tbody>
      <tr>
        <td>Tip categories (incl. homepage <span class="mono">homeslug</span>)</td>
        <td><span class="mono">admin/app/Filament/Resources/GameCatResource.php</span> → section <strong>SEO</strong></td>
        <td><span class="mono">game_cats</span></td>
      </tr>
      <tr>
        <td>Legal &amp; site pages</td>
        <td><span class="mono">admin/app/Filament/Resources/SiteLegalPageResource.php</span></td>
        <td><span class="mono">seo_pages</span></td>
      </tr>
      <tr>
        <td>Other SEO pages</td>
        <td><span class="mono">admin/app/Filament/Resources/SeoPageResource.php</span></td>
        <td><span class="mono">seo_pages</span></td>
      </tr>
    </tbody>
  </table>
  <p>Routes that pick which Livewire page runs: <span class="mono">front/routes/web.php</span>.</p>

  <h2>4. Quick map — where each page is edited</h2>
  <table>
    <thead>
      <tr>
        <th>Page type</th>
        <th>Public example</th>
        <th>Admin menu</th>
        <th>Editable?</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Homepage</strong></td>
        <td><span class="mono">/</span></td>
        <td>Content Management → <strong>Tip categories</strong> → row with slug <span class="mono">homeslug</span></td>
        <td><span class="badge">Yes</span></td>
      </tr>
      <tr>
        <td><strong>Tip categories</strong></td>
        <td><span class="mono">/over-2-5</span>, <span class="mono">/btts</span>, etc.</td>
        <td>Content Management → <strong>Tip categories</strong></td>
        <td><span class="badge">Yes</span></td>
      </tr>
      <tr>
        <td><strong>Static / legal pages</strong></td>
        <td><span class="mono">/about</span>, <span class="mono">/privacy</span>, <span class="mono">/contact</span>, etc.</td>
        <td>Content Management → <strong>Legal &amp; site pages</strong></td>
        <td><span class="badge">Yes</span></td>
      </tr>
      <tr>
        <td><strong>Other SEO pages</strong></td>
        <td>Custom landings like extra marketing URLs</td>
        <td>Content Management → <strong>Other SEO pages</strong></td>
        <td><span class="badge">Yes</span></td>
      </tr>
      <tr>
        <td><strong>Match pages</strong></td>
        <td><span class="mono">/match/…</span></td>
        <td>—</td>
        <td><span class="badge badge-auto">Auto</span></td>
      </tr>
      <tr>
        <td><strong>League pages</strong></td>
        <td><span class="mono">/league/…</span></td>
        <td>—</td>
        <td><span class="badge badge-auto">Auto</span></td>
      </tr>
    </tbody>
  </table>

  <div class="note">
    <strong>SEO fields to edit:</strong> open the page record, scroll to the <strong>SEO</strong> section, then update
    <em>Meta description</em> (and optionally <em>Meta keywords</em>). Click <strong>Save</strong>.
    Aim for about <strong>140–160 characters</strong> for the meta description.
  </div>

  <h2>5. Homepage meta description</h2>
  <ol class="steps">
    <li>Open <span class="mono">{{ url("/admin") }}</span> and log in</li>
    <li>Or go directly to <span class="mono">{{ url("/admin/game-cats") }}</span></li>
    <li>Go to <strong>Content Management → Tip categories</strong></li>
    <li>Find the category with slug <span class="mono">homeslug</span> (this is the homepage SEO row)</li>
    <li>Click <strong>Edit</strong></li>
    <li>Scroll to the <strong>SEO</strong> section</li>
    <li>Edit <strong>Meta description</strong></li>
    <li>Click <strong>Save</strong></li>
    <li>Check the live homepage: view page source and confirm <span class="mono">&lt;meta name="description"&gt;</span></li>
  </ol>
  <div class="warn">
    Do <strong>not</strong> change the <span class="mono">homeslug</span> URL slug. Only edit title, on-page copy, and SEO fields.
  </div>

  <h2>6. Tip category pages</h2>
  <p>Examples: Over 2.5, BTTS, Correct Score, Free tips category pages.</p>
  <ol class="steps">
    <li>Go to <strong>Content Management → Tip categories</strong></li>
    <li>Open the tip category you want (use the slug column to match the public URL)</li>
    <li>In the <strong>SEO</strong> section, edit <strong>Meta description</strong></li>
    <li>Save, then open <span class="mono">https://dailysuretips.com/{slug}</span> to verify</li>
  </ol>
  <p><strong>Also useful on the same form:</strong></p>
  <ul class="compact">
    <li><strong>Title</strong> — used in SEO title fallback</li>
    <li><strong>Head 1 / Head 2</strong> — on-page headlines (Head 2 can also backfill meta if meta description is empty)</li>
    <li><strong>Content</strong> — page body / footer SEO copy</li>
  </ul>

  <h2>7. Static pages (Legal &amp; site pages)</h2>
  <p>These are fixed site pages managed under <strong>Legal &amp; site pages</strong>:</p>
  <table>
    <thead>
      <tr><th>Admin page name</th><th>Public URL</th></tr>
    </thead>
    <tbody>
      <tr><td>About us</td><td><span class="mono">/about</span></td></tr>
      <tr><td>Privacy policy</td><td><span class="mono">/privacy</span></td></tr>
      <tr><td>Disclaimer</td><td><span class="mono">/disclaimer</span></td></tr>
      <tr><td>Refund policy</td><td><span class="mono">/refund</span></td></tr>
      <tr><td>Terms &amp; conditions</td><td><span class="mono">/terms</span></td></tr>
      <tr><td>Our partners</td><td><span class="mono">/partners</span></td></tr>
      <tr><td>Contact us</td><td><span class="mono">/contact</span></td></tr>
      <tr><td>How to pay</td><td><span class="mono">/how-to-pay</span></td></tr>
      <tr><td>Blog index</td><td><span class="mono">/blog</span></td></tr>
    </tbody>
  </table>
  <ol class="steps">
    <li>Open <span class="mono">{{ url("/admin/site-legal-pages") }}</span></li>
    <li>Or: <strong>Content Management → Legal &amp; site pages</strong></li>
    <li>Edit the page (e.g. About us)</li>
    <li>Update <strong>SEO → Meta description</strong></li>
    <li>Ensure <strong>Status</strong> is <strong>Published</strong></li>
    <li>Save and preview with the <strong>View</strong> action if available</li>
  </ol>

  <h2>8. Other SEO pages (custom landings)</h2>
  <ol class="steps">
    <li>Open <span class="mono">{{ url("/admin/seo-pages") }}</span></li>
    <li>Or: <strong>Content Management → Other SEO pages</strong></li>
    <li>Create or edit a page</li>
    <li>Set <strong>Title</strong>, <strong>Slug</strong> (public URL path), and body content</li>
    <li>In <strong>SEO</strong>, set <strong>Meta description</strong></li>
    <li>Set status to <strong>Published</strong> and save</li>
  </ol>
  <div class="note">
    Public URL becomes <span class="mono">https://dailysuretips.com/{slug}</span>.
    Avoid slugs already used by tip categories or legal pages.
  </div>

  <h2>9. Match pages — currently auto-generated</h2>
  <p>Match prediction pages do <strong>not</strong> have a meta description field in the admin.</p>
  <div class="warn">
    The site automatically builds the description like:<br>
    <em>“Team A vs Team B prediction, H2H, statistics and betting tips on Dailysuretips.”</em>
  </div>
  <p>To change wording for all match pages, that requires a developer update — see <strong>section 3D</strong> (<span class="mono">MatchPageData.php</span>), not a CMS edit.</p>

  <h2>10. League pages — currently auto-generated</h2>
  <p>League pages also do <strong>not</strong> have editable meta description fields in admin
  (<strong>Leagues &amp; Countries → Leagues</strong> only manages name, logo, featured flag, etc.).</p>
  <div class="warn">
    The site automatically builds the description like:<br>
    <em>“Dailysuretips {League Name} predictions, upcoming matches, and expert tips for {Country}.”</em>
  </div>
  <p>Custom per-league SEO would need a developer feature if required later.</p>

  <h2>11. Tips for good meta descriptions</h2>
  <ul class="compact">
    <li>Keep roughly <strong>140–160 characters</strong></li>
    <li>Include the main keyword naturally (e.g. “free football predictions”)</li>
    <li>Write for humans — clear benefit, not keyword stuffing</li>
    <li>Make each page unique (homepage ≠ tip category ≠ about page)</li>
    <li>After saving, hard-refresh the public page (Ctrl+F5) and re-check page source</li>
  </ul>

  <h2>12. Checklist after editing</h2>
  <ol class="steps">
    <li>Saved successfully in admin</li>
    <li>Page status is Published (for legal / SEO pages)</li>
    <li>Opened the public URL</li>
    <li>Viewed page source → found updated <span class="mono">meta name="description"</span></li>
    <li>(Optional) Shared the URL in Facebook/Twitter debugger to refresh social preview</li>
  </ol>

  <div class="footer">
    Dailysuretips — Client SEO guide (meta descriptions)<br>
    Generated for admin panel at admin.dailysuretips.com · Front site dailysuretips.com
  </div>
