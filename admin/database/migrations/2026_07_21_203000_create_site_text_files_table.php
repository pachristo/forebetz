<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Creates site_text_files and seeds robots.txt / ads.txt / llms.txt from
 * database/seeders/data/*.txt (shipped with this admin app for online migrate).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_text_files')) {
            Schema::create('site_text_files', function (Blueprint $table) {
                $table->id();
                $table->string('key', 32)->unique();
                $table->string('label', 120);
                $table->longText('content')->nullable();
                $table->timestamps();
            });
        }

        $this->seedTextFiles();
    }

    public function down(): void
    {
        Schema::dropIfExists('site_text_files');
    }

    private function seedTextFiles(): void
    {
        $front = rtrim((string) (env('FRONT_APP_URL') ?: env('MAIN_URL') ?: 'https://winningpredict.com'), '/');
        $dataDir = base_path('database'.DIRECTORY_SEPARATOR.'seeders'.DIRECTORY_SEPARATOR.'data');

        $robots = $this->readSeedFile($dataDir, 'robots.txt', $this->defaultRobots($front));
        $robots = (string) (preg_replace('/(?im)^Sitemap:\s*.+$/m', 'Sitemap: '.$front.'/wp-sitemap.xml', $robots) ?? $robots);

        $ads = $this->readSeedFile($dataDir, 'ads.txt', '');
        $llms = $this->readSeedFile($dataDir, 'llms.txt', $this->defaultLlms($front));

        $rows = [
            ['key' => 'robots', 'label' => 'robots.txt', 'content' => rtrim($robots)."\n"],
            ['key' => 'ads', 'label' => 'ads.txt', 'content' => rtrim($ads)."\n"],
            ['key' => 'llms', 'label' => 'llms.txt', 'content' => rtrim($llms)."\n"],
        ];

        $now = now();
        foreach ($rows as $row) {
            $existing = DB::table('site_text_files')->where('key', $row['key'])->first();
            if ($existing && trim((string) $existing->content) !== '') {
                continue;
            }

            if ($existing) {
                DB::table('site_text_files')->where('key', $row['key'])->update([
                    'label' => $row['label'],
                    'content' => $row['content'],
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('site_text_files')->insert([
                    'key' => $row['key'],
                    'label' => $row['label'],
                    'content' => $row['content'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function readSeedFile(string $dataDir, string $filename, string $fallback): string
    {
        $path = $dataDir.DIRECTORY_SEPARATOR.$filename;
        if (is_file($path)) {
            $content = (string) file_get_contents($path);
            if (trim($content) !== '') {
                return $content;
            }
        }

        return $fallback;
    }

    private function defaultRobots(string $front): string
    {
        return "User-agent: *\n"
            ."Disallow:\n\n"
            .'Sitemap: '.$front."/wp-sitemap.xml\n\n"
            ."# Disallow specific directories\n"
            ."Disallow: /admin/\n"
            ."Disallow: /login/\n"
            ."Disallow: /private/\n"
            ."Disallow: /tmp/\n\n"
            ."# Disallow URLs with specific query parameters\n"
            ."Disallow: /*?type=\n"
            ."Disallow: /*?page=\n"
            ."Disallow: /*?dt=\n"
            ."Disallow: /*?date=\n\n"
            ."# Disallow URLs with index.php in the path\n"
            ."Disallow: /*index.php\n\n"
            ."User-agent: Googlebot\n"
            ."Allow: /\n\n"
            ."User-agent: Bingbot\n"
            ."Allow: /\n\n"
            ."User-agent: BadBot\n"
            ."Disallow: /\n\n"
            ."Disallow: /*?sessionid=\n"
            ."Disallow: /*?sort=\n\n"
            ."User-agent: *\n"
            ."Allow: /*.css\$\n"
            ."Allow: /*.js\$\n"
            ."Allow: /*.jpg\$\n"
            ."Allow: /*.png\$\n"
            ."Allow: /*.gif\$\n"
            ."Allow: /*.svg\$\n"
            ."Allow: /*.xml\$\n"
            ."Allow: /*.json\$\n"
            ."Allow: /*.txt\$\n";
    }

    private function defaultLlms(string $front): string
    {
        return "# WinningPredict\n\n"
            ."> Football predictions, VIP tips, and sports news.\n\n"
            ."## Site\n\n"
            ."- [Home]({$front}/)\n"
            ."- [VIP]({$front}/vip)\n"
            ."- [Free tips]({$front}/free-tips)\n"
            ."- [Blog]({$front}/blog)\n"
            ."- [Sitemap]({$front}/wp-sitemap.xml)\n";
    }
};
