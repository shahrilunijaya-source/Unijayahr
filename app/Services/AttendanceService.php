<?php
namespace App\Services;

use App\Exceptions\AlreadyClockedInException;
use App\Exceptions\AlreadyClockedOutException;
use App\Exceptions\NotClockedInException;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    public function clockIn(
        User $user,
        float $lat,
        float $lng,
        ?float $accuracy,
        string $photoBase64
    ): AttendanceRecord {
        return DB::transaction(function () use ($user, $lat, $lng, $accuracy, $photoBase64) {
            // First try to find with lock (common case — already exists)
            $record = AttendanceRecord::where('user_id', $user->id)
                ->whereDate('date', today())
                ->lockForUpdate()
                ->first();

            // If not found, insert fresh record
            if ($record === null) {
                $record = AttendanceRecord::create([
                    'user_id' => $user->id,
                    'date'    => today(),
                ]);
                // Re-fetch with lock after creation
                $record = AttendanceRecord::where('user_id', $user->id)
                    ->whereDate('date', today())
                    ->lockForUpdate()
                    ->first();
            }

            if ($record->clock_in_at !== null) {
                throw new AlreadyClockedInException('Already clocked in today.');
            }

            $clockInAt  = Carbon::now();
            $photoPath  = $this->storePhoto($user->id, today()->toDateString(), 'in', $photoBase64);

            $expected   = Carbon::parse(today()->toDateString() . ' ' . ($user->work_start_time ?? '10:00:00'));
            // Positive when clock_in_at is AFTER expected start (late)
            $lateBy     = (int) max(0, $expected->diffInMinutes($clockInAt, false));
            $isLate     = $lateBy > 5;

            $record->fill([
                'clock_in_at'         => $clockInAt,
                'clock_in_lat'        => $lat,
                'clock_in_lng'        => $lng,
                'clock_in_accuracy'   => $accuracy,
                'clock_in_photo_path' => $photoPath,
                'is_late'             => $isLate,
                'late_minutes'        => $isLate ? $lateBy : null,
            ])->save();

            if ($isLate && $lateBy > 15 && $user->superior) {
                Notification::make()
                    ->title("{$user->name} clocked in {$lateBy} minutes late.")
                    ->warning()
                    ->sendToDatabase($user->superior);
            }

            return $record->fresh();
        });
    }

    public function clockOut(
        User $user,
        float $lat,
        float $lng,
        ?float $accuracy,
        string $photoBase64
    ): AttendanceRecord {
        return DB::transaction(function () use ($user, $lat, $lng, $accuracy, $photoBase64) {
            $record = AttendanceRecord::where('user_id', $user->id)
                ->whereDate('date', today())
                ->lockForUpdate()
                ->first();

            if ($record === null || $record->clock_in_at === null) {
                throw new NotClockedInException('No clock-in found for today.');
            }

            if ($record->clock_out_at !== null) {
                throw new AlreadyClockedOutException('Already clocked out today.');
            }

            $clockOutAt  = Carbon::now();
            $photoPath   = $this->storePhoto($user->id, today()->toDateString(), 'out', $photoBase64);
            $totalHours  = round($clockOutAt->diffInMinutes($record->clock_in_at) / 60, 2);

            $record->fill([
                'clock_out_at'          => $clockOutAt,
                'clock_out_lat'         => $lat,
                'clock_out_lng'         => $lng,
                'clock_out_accuracy'    => $accuracy,
                'clock_out_photo_path'  => $photoPath,
                'total_hours'           => $totalHours,
            ])->save();

            return $record->fresh();
        });
    }

    private function storePhoto(int $userId, string $date, string $which, string $base64): string
    {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $binary = base64_decode($base64, true);

        if ($binary === false || strlen($binary) > 4 * 1024 * 1024) {
            throw new \InvalidArgumentException('Invalid or oversized photo.');
        }

        if (getimagesizefromstring($binary) === false) {
            throw new \InvalidArgumentException('Uploaded file is not a valid image.');
        }

        $ts   = now()->format('His');
        $path = "attendance-photos/{$userId}/{$date}/{$which}_{$ts}.jpg";

        Storage::disk('local')->put($path, $binary);

        return $path;
    }
}
