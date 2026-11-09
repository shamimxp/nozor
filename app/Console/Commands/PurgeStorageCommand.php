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
            $freed += $this->purgeDirectory(storage_path('logs'));
        }

        if ($onlySessions || $all) {
            $freed += $this->purgeDirectory(storage_path('framework/sessions'));
        }

        if ($all) {
            $freed += $this->purgeDirectory(storage_path('framework/views'));
            $freed += $this->purgeDirectory(storage_path('framework/cache'));
            $freed += $this->purgeDirectory(storage_path('debugbar'));
            $freed += $this->purgeDirectory(storage_path('app/tmp'));
        }

        $this->info('Storage purge complete. Approx freed: ' . $this->formatBytes($freed));

        return self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $keep
     */
    protected function purgeDirectory(string $directory, array $keep = ['.gitignore']): int
    {
        if (! is_dir($directory)) {
            return 0;
        }

        $bytes = 0;

        try {
            $files = File::allFiles($directory);
            foreach ($files as $file) {
                if (in_array($file->getFilename(), $keep, true)) {
                    continue;
                }

                $size = (int) $file->getSize();
                $path = $file->getRealPath();

                if (@unlink($path)) {
                    $bytes += $size;
                } elseif (@file_put_contents($path, '') !== false) {
                    $bytes += $size;
                }
            }
        } catch (\Throwable $e) {
            // Ignore directory iteration errors
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
