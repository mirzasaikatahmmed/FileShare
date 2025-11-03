<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupExpiredFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup
                            {--days=7 : Number of days after which files should be deleted}
                            {--dry-run : Run without actually deleting files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically delete files that are older than specified days (default: 7 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Starting cleanup of files older than {$days} days...");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be deleted');
        }

        // Calculate the cutoff date (7 days ago)
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cutoff date: {$cutoffDate->toDateTimeString()}");

        // Find files older than the cutoff date
        $expiredFiles = File::where('created_at', '<', $cutoffDate)
            ->get();

        if ($expiredFiles->isEmpty()) {
            $this->info('No expired files found.');
            return 0;
        }

        $this->info("Found {$expiredFiles->count()} expired files.");

        $deletedCount = 0;
        $failedCount = 0;
        $totalSize = 0;

        // Progress bar
        $bar = $this->output->createProgressBar($expiredFiles->count());
        $bar->start();

        foreach ($expiredFiles as $file) {
            try {
                $fileSize = $file->file_size;

                if (!$dryRun) {
                    // Delete the actual file from storage
                    if (Storage::disk('private')->exists($file->file_path)) {
                        Storage::disk('private')->delete($file->file_path);
                    }

                    // Delete the QR code from public storage
                    if ($file->qr_code_path && Storage::disk('public')->exists($file->qr_code_path)) {
                        Storage::disk('public')->delete($file->qr_code_path);
                    }

                    // Log the deletion
                    ActivityLog::create([
                        'file_id' => $file->id,
                        'action' => 'auto_delete',
                        'description' => "File automatically deleted after {$days} days",
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'System Cron Job',
                    ]);

                    // Delete the file record from database (soft delete)
                    $file->delete();
                }

                $deletedCount++;
                $totalSize += $fileSize;

            } catch (\Exception $e) {
                $failedCount++;
                $this->error("\nFailed to delete file {$file->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display summary
        $this->info('==============================================');
        $this->info('Cleanup Summary:');
        $this->info('==============================================');
        $this->info("Files processed: {$expiredFiles->count()}");

        if ($dryRun) {
            $this->warn("Would delete: {$deletedCount} files");
        } else {
            $this->info("Successfully deleted: {$deletedCount} files");
        }

        if ($failedCount > 0) {
            $this->error("Failed: {$failedCount} files");
        }

        $this->info("Total space " . ($dryRun ? "to be freed" : "freed") . ": " . $this->formatBytes($totalSize));
        $this->info('==============================================');

        return 0;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
