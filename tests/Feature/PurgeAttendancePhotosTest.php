<?php
namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurgeAttendancePhotosTest extends TestCase
{
    use RefreshDatabase;

    public function test_photos_older_than_30_days_are_deleted_and_paths_nulled(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        // Create a record 31 days old with both photos
        $oldRecord = AttendanceRecord::factory()->create([
            'user_id'              => $user->id,
            'date'                 => Carbon::now()->subDays(31)->toDateString(),
            'clock_in_photo_path'  => 'attendance-photos/1/2026-04-01/in_100000.jpg',
            'clock_out_photo_path' => 'attendance-photos/1/2026-04-01/out_190000.jpg',
        ]);
        Storage::disk('local')->put($oldRecord->clock_in_photo_path, 'fake-image');
        Storage::disk('local')->put($oldRecord->clock_out_photo_path, 'fake-image');

        $this->artisan('attendance:purge-photos')->assertExitCode(0);

        Storage::disk('local')->assertMissing($oldRecord->clock_in_photo_path);
        Storage::disk('local')->assertMissing($oldRecord->clock_out_photo_path);

        $oldRecord->refresh();
        $this->assertNull($oldRecord->clock_in_photo_path);
        $this->assertNull($oldRecord->clock_out_photo_path);
    }

    public function test_photos_within_30_days_are_untouched(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $recentRecord = AttendanceRecord::factory()->create([
            'user_id'             => $user->id,
            'date'                => Carbon::now()->subDays(29)->toDateString(),
            'clock_in_photo_path' => 'attendance-photos/1/2026-05-03/in_100000.jpg',
        ]);
        Storage::disk('local')->put($recentRecord->clock_in_photo_path, 'fake-image');

        $this->artisan('attendance:purge-photos')->assertExitCode(0);

        Storage::disk('local')->assertExists($recentRecord->clock_in_photo_path);
        $recentRecord->refresh();
        $this->assertNotNull($recentRecord->clock_in_photo_path);
    }

    public function test_dry_run_does_not_delete_files(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $oldRecord = AttendanceRecord::factory()->create([
            'user_id'             => $user->id,
            'date'                => Carbon::now()->subDays(31)->toDateString(),
            'clock_in_photo_path' => 'attendance-photos/1/2026-04-01/in_100000.jpg',
        ]);
        Storage::disk('local')->put($oldRecord->clock_in_photo_path, 'fake-image');

        $this->artisan('attendance:purge-photos', ['--dry-run' => true])->assertExitCode(0);

        Storage::disk('local')->assertExists($oldRecord->clock_in_photo_path);
        $oldRecord->refresh();
        $this->assertNotNull($oldRecord->clock_in_photo_path);
    }
}
