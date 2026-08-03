<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Backup\BackupDestination\BackupDestination;

class SettingsController extends Controller
{
    public function index()
    {
        $info = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env'         => config('app.env'),
            'app_debug'       => config('app.debug') ? 'Enabled' : 'Disabled',
            'db_driver'       => config('database.default'),
            'cache_driver'    => config('cache.default'),
            'queue_driver'    => config('queue.default'),
        ];

        // DB size
        $info['db_size'] = $this->databaseSize();

        // Storage size
        $storagePath = storage_path('app');
        $info['storage_size'] = is_dir($storagePath)
            ? $this->formatBytes($this->dirSize($storagePath))
            : 'N/A';

        // Media library size
        $mediaPath = storage_path('media-library');
        $info['media_size'] = is_dir($mediaPath)
            ? $this->formatBytes($this->dirSize($mediaPath))
            : '0 B';

        // Cache size
        $cachePath = storage_path('framework/cache');
        $info['cache_size'] = is_dir($cachePath)
            ? $this->formatBytes($this->dirSize($cachePath))
            : '0 B';

        $backups = $this->listBackups();

        return view('settings.index', compact('info', 'backups'));
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        Setting::instance()->addMediaFromRequest('logo')->toMediaCollection('logo');

        return back()->with('success', 'Logo updated.');
    }

    public function destroyLogo()
    {
        Setting::instance()->clearMediaCollection('logo');

        return back()->with('success', 'Logo removed.');
    }

    public function clearCache(Request $request)
    {
        $cleared = [];
        $errors  = [];

        $commands = [
            'Application Cache' => 'cache:clear',
            'Config Cache'      => 'config:clear',
            'Route Cache'       => 'route:clear',
            'View Cache'        => 'view:clear',
            'Compiled Files'    => 'clear-compiled',
        ];

        foreach ($commands as $label => $command) {
            try {
                Artisan::call($command);
                $cleared[] = $label;
            } catch (\Throwable $e) {
                $errors[] = $label . ': ' . $e->getMessage();
            }
        }

        // Also clear temp media directory
        $tempPath = storage_path('media-library/temp');
        if (is_dir($tempPath)) {
            File::deleteDirectory($tempPath);
            $cleared[] = 'Media Temp Files';
        }

        if (empty($errors)) {
            $msg = 'All caches cleared: ' . implode(', ', $cleared) . '.';
            return back()->with('cache_success', $msg);
        }

        return back()
            ->with('cache_success', count($cleared) . ' cache(s) cleared.')
            ->with('cache_errors', $errors);
    }

    public function runBackup()
    {
        // Queued rather than run inline: a full mysqldump can take a while on a large
        // database, and PHP's built-in dev server mishandles spawning subprocesses
        // (like mysqldump) mid-request — running it via the queue avoids both problems.
        Artisan::queue('backup:run', ['--only-db' => true]);

        return back()->with('backup_success', 'Backup started — it will appear in the list below shortly.');
    }

    public function downloadBackup(string $disk, string $filename)
    {
        $backup = $this->findBackup($disk, $filename);
        abort_unless($backup, 404);

        return $backup->disk()->download($backup->path());
    }

    public function destroyBackup(string $disk, string $filename)
    {
        $backup = $this->findBackup($disk, $filename);
        abort_unless($backup, 404);

        $backup->delete();

        return back()->with('backup_success', 'Backup deleted.');
    }

    private function findBackup(string $diskName, string $filename): ?\Spatie\Backup\BackupDestination\Backup
    {
        $destination = BackupDestination::create($diskName, config('backup.backup.name'));

        foreach ($destination->backups() as $backup) {
            if (basename($backup->path()) === $filename) {
                return $backup;
            }
        }

        return null;
    }

    private function listBackups(): array
    {
        $backupName = config('backup.backup.name');
        $items = [];

        foreach (config('backup.backup.destination.disks') as $diskName) {
            $destination = BackupDestination::create($diskName, $backupName);

            foreach ($destination->backups() as $backup) {
                $items[] = [
                    'disk'     => $diskName,
                    'filename' => basename($backup->path()),
                    'date'     => $backup->date(),
                    'size'     => $this->formatBytes((int) $backup->sizeInBytes()),
                ];
            }
        }

        usort($items, fn ($a, $b) => $b['date']->timestamp <=> $a['date']->timestamp);

        return $items;
    }

    private function databaseSize(): string
    {
        if (config('database.default') !== 'mysql') {
            return 'N/A';
        }

        $result = DB::selectOne(
            'SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?',
            [config('database.connections.mysql.database')]
        );

        return $result?->size ? $this->formatBytes((int) $result->size) : '0 B';
    }

    private function dirSize(string $path): int
    {
        $size = 0;
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
