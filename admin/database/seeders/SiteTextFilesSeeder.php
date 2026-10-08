<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds robots.txt / ads.txt / llms.txt into site_text_files from database/seeders/data.
 * Does not overwrite rows that already have content (safe for production).
 */
class SiteTextFilesSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('site_text_files')) {
            $this->command?->warn('site_text_files table missing — run migrations first.');

            return;
        }

        $front = rtrim((string) (env('FRONT_APP_URL') ?: env('MAIN_URL') ?: 'https://winningpredict.com'), '/');
        $dataDir = database_path('seeders'.DIRECTORY_SEPARATOR.'data');

        $robots = $this->read($dataDir, 'robots.txt', $this->defaultRobots($front));
        $robots = (string) (preg_replace('/(?im)^Sitemap:\s*.+$/m', 'Sitemap: '.$front.'/wp-sitemap.xml', $robots) ?? $robots);
        $ads = $this->read($dataDir, 'ads.txt', '');
        $llms = $this->read($dataDir, 'llms.txt', $this->defaultLlms($front));

        $rows = [
            ['key' => 'robots', 'label' => 'robots.txt', 'content' => rtrim($robots)."\n"],
            ['key' => 'ads', 'label' => 'ads.txt', 'content' => rtrim($ads)."\n"],
            ['key' => 'llms', 'label' => 'llms.txt', 'content' => rtrim($llms)."\n"],
        ];

        $now = now();
        foreach ($rows as $row) {
            $existing = DB::table('site_text_files')->where('key', $row['key'])->first();
            if ($existing && trim((string) $existing->content) !== '') {
                $this->command?->info("Skip {$row['key']} (already has content).");

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

            $this->command?->info("Seeded {$row['key']} (".strlen($row['content']).' bytes).');
        }
    }

    private function read(string $dataDir, string $filename, string $fallback): string
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
        return "User-agent: *\nDisallow:\n\nSitemap: {$front}/wp-sitemap.xml\n";
    }

    private function defaultLlms(string $front): string
    {
        return "# WinningPredict\n\n> Football predictions, VIP tips, and sports news.\n\n## Site\n\n- [Home]({$front}/)\n- [Sitemap]({$front}/wp-sitemap.xml)\n";
    }
}
