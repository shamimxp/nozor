<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PurgeStorageCommand extends Command
{
    protected $signature = 'storage:purge
                            {--logs : Only purge log files}
                            {--sessions : Only purge session files}
                            {--all : Purge logs, sessions, views, cache, debugbar}';

    protected $description = 'Free disk space by purging Laravel storage junk (logs, sessions, views, cache, debugbar)';

    public function handle(): int
    {
        $onlyLogs = $this->option('logs');
        $onlySessions = $this->option('sessions');
        $all = $this->option('all') || (! $onlyLogs && ! $onlySessions);

        $freed = 0;

        if ($onlyLogs || $all) {
            $freed += $this->purgeDirectory(storage_path('logs'), ['*.log']);
        }

        if ($onlySessions || $all) {
            $freed += $this->purgeDirectory(storage_path('framework/sessions'), ['*'], ['.gitignore']);
        }

        if ($all) {
            $freed += $this->purgeDirectory(storage_path('framework/views'), ['*'], ['.gitignore']);
            $freed += $this->purgeDirectory(storage_path('framework/cache/data'), ['*'], ['.gitignore']);
            $freed += $this->purgeDirectory(storage_path('debugbar'), ['*'], ['.gitignore']);
        }

        $this->info('Storage purge complete. Approx freed: ' . $this->formatBytes($freed));

        return self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $patterns
     * @param  array<int, string>  $keep
     */
    protected function purgeDirectory(string $directory, array $patterns = ['*'], array $keep = ['.gitignore']): int
    {
        if (! is_dir($directory)) {
            return 0;
        }

        $bytes = 0;

        foreach ($patterns as $pattern) {
            foreach (File::glob(rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $pattern) ?: [] as $path) {
                if (! is_file($path)) {
                    continue;
                }

                if (in_array(basename($path), $keep, true)) {
                    continue;
                }

                $size = (int) @filesize($path);
                if (@unlink($path)) {
                    $bytes += $size;
                } else {
                    // Truncate if unlink fails (file locked / open handle)
                    if (@file_put_contents($path, '') !== false) {
                        $bytes += $size;
                    }
                }
            }
        }

        return $bytes;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return round($bytes / 1048576, 2) . ' MB';
    }
}
