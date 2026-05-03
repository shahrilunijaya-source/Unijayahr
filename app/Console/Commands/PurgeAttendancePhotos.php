<?php
namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeAttendancePhotos extends Command
{
    protected $signature = 'attendance:purge-photos {--dry-run : List what would be deleted without deleting}';
    protected $description = 'Delete attendance photos older than 30 days and null their paths.';

    public function handle(): int
    {
        $dryRun  = $this->option('dry-run');
        $cutoff  = now()->subDays(30)->toDateString();
        $deleted = 0;

        AttendanceRecord::where('date', '<', $cutoff)
            ->where(function ($q) {
                $q->whereNotNull('clock_in_photo_path')
                  ->orWhereNotNull('clock_out_photo_path');
            })
            ->chunkById(200, function ($records) use ($dryRun, &$deleted) {
                foreach ($records as $record) {
                    foreach (['clock_in_photo_path', 'clock_out_photo_path'] as $col) {
                        if ($record->$col) {
                            if ($dryRun) {
                                $this->line("[dry-run] Would delete: {$record->$col}");
                            } else {
                                Storage::disk('local')->delete($record->$col);
                            }
                            $deleted++;
                        }
                    }

                    if (!$dryRun) {
                        $record->clock_in_photo_path  = null;
                        $record->clock_out_photo_path = null;
                        $record->save();
                    }
                }
            });

        $label = $dryRun ? 'Would delete' : 'Deleted';
        $this->info("{$label} {$deleted} photo(s).");

        return self::SUCCESS;
    }
}
