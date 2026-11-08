<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PruneDatabaseSessionsCommand extends Command
{
    protected $signature = 'sessions:prune {--days=7 : Delete sessions idle longer than this many days}';

    protected $description = 'Delete database sessions idle longer than the retention window, and drop expired cache rows';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $sessionCutoff = time() - ($days * 86400);
        $now = time();

        $sessions = 0;
        if (Schema::hasTable('sessions')) {
            $sessions = DB::table('sessions')->where('last_activity', '<=', $sessionCutoff)->delete();
        }

        $cache = 0;
        if (Schema::hasTable('cache')) {
            $cache = DB::table('cache')->where('expiration', '<=', $now)->delete();
        }

        $locks = 0;
        if (Schema::hasTable('cache_locks')) {
            $locks = DB::table('cache_locks')->where('expiration', '<=', $now)->delete();
        }

        $this->info("Pruned {$sessions} session(s) idle longer than {$days} days, {$cache} expired cache row(s), {$locks} expired lock(s).");

        return self::SUCCESS;
    }
}
