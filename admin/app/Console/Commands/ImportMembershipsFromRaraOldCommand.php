<?php

namespace App\Console\Commands;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportMembershipsFromRaraOldCommand extends Command
{
    protected $signature = 'memberships:import-from-rara-old
        {--dry-run : List counts only, no writes}
        {--no-clear : Do not empty memberships / subscriptions before import}
        {--force : Skip confirmation when clearing target tables}';

    protected $description = 'Clear memberships (optional), then import users from rara_old.users into memberships';

    public function handle(): int
    {
        $source = 'rara_old';
        $target = config('database.default');

        try {
            DB::connection($source)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Cannot connect to [{$source}]: ".$e->getMessage());

            return self::FAILURE;
        }

        $total = (int) DB::connection($source)->table('users')->count();
        $withPassword = (int) DB::connection($source)->table('users')
            ->whereNotNull('password')
            ->where('password', '!=', '')
            ->count();

        $this->info("Source [{$source}] users: {$total}, with password: {$withPassword}. Target connection: [{$target}].");

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database changes.');

            return self::SUCCESS;
        }

        if (! $this->option('no-clear')) {
            if (! $this->option('force') && ! $this->confirm('This will DELETE all rows in memberships and member_subscriptions (and Spatie pivots for memberships). Continue?', true)) {
                $this->warn('Aborted.');

                return self::FAILURE;
            }

            $this->clearTargetTables();
        }

        $imported = 0;
        $skipped = 0;

        DB::connection($source)->table('users')->orderBy('id')->chunkById(500, function ($rows) use (&$imported, &$skipped) {
            $batch = [];
            $now = now()->format('Y-m-d H:i:s');

            foreach ($rows as $row) {
                $email = trim((string) ($row->email ?? ''));
                $password = (string) ($row->password ?? '');

                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;

                    continue;
                }

                if ($password === '' || ! $this->isBcryptHash($password)) {
                    $skipped++;

                    continue;
                }

                $name = $this->resolveName($row);
                if ($name === '') {
                    $name = strstr($email, '@', true) ?: $email;
                }

                $batch[] = [
                    'name' => mb_substr($name, 0, 255),
                    'email' => mb_substr($email, 0, 255),
                    'phone' => $this->nullablePhone($row->phone ?? null),
                    'password' => $password,
                    'subscription_status' => (int) ($row->subscription_status ?? 0),
                    'subscription_id' => $this->nullableId($row->subscription_id ?? null),
                    'sub_date' => $this->formatDateTime($row->date_subscribed ?? null),
                    'next_due_date' => $this->formatDateTime($row->next_due_date ?? null),
                    'is_flagged' => (bool) ($row->flag ?? 0),
                    'country' => $this->nullableString($row->country ?? null, 255),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($batch !== []) {
                DB::table('memberships')->insert($batch);
                $imported += count($batch);
            }
        }, 'id');

        $this->info("Imported: {$imported}, skipped (no email / invalid email / no bcrypt password / empty name fallback ok): {$skipped}.");

        return self::SUCCESS;
    }

    private function clearTargetTables(): void
    {
        $membershipMorph = Membership::class;

        Schema::disableForeignKeyConstraints();

        try {
            $permission = config('permission.table_names', []);

            foreach (['model_has_roles', 'model_has_permissions'] as $key) {
                if (empty($permission[$key])) {
                    continue;
                }
                $table = $permission[$key];
                if (Schema::hasTable($table)) {
                    DB::table($table)->where('model_type', $membershipMorph)->delete();
                }
            }

            if (Schema::hasTable('member_subscriptions')) {
                DB::table('member_subscriptions')->truncate();
            }

            if (Schema::hasTable('memberships')) {
                DB::table('memberships')->truncate();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->info('Target memberships and member_subscriptions cleared.');
    }

    private function resolveName(object $row): string
    {
        foreach (['full_name', 'name', 'username'] as $col) {
            if (isset($row->{$col}) && is_string($row->{$col}) && trim($row->{$col}) !== '') {
                return trim($row->{$col});
            }
        }

        $first = isset($row->first_name) ? trim((string) $row->first_name) : '';
        $last = isset($row->last_name) ? trim((string) $row->last_name) : '';
        $combined = trim($first.' '.$last);

        return $combined;
    }

    private function isBcryptHash(string $hash): bool
    {
        return str_starts_with($hash, '$2y$')
            || str_starts_with($hash, '$2a$')
            || str_starts_with($hash, '$2b$');
    }

    private function nullablePhone(mixed $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        return mb_substr((string) $phone, 0, 191);
    }

    private function nullableString(mixed $v, int $max): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }

        return mb_substr((string) $v, 0, $max);
    }

    private function nullableId(mixed $id): ?int
    {
        if ($id === null || $id === '') {
            return null;
        }
        $n = (int) $id;

        return $n > 0 ? $n : null;
    }

    private function formatDateTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
