<?php

namespace Tests\Unit;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceRecordTest extends TestCase
{
    use RefreshDatabase;

    // 1. Casts
    public function test_casts_are_correct(): void
    {
        $user = User::factory()->create();
        $record = AttendanceRecord::factory()->create([
            'user_id'      => $user->id,
            'date'         => '2026-05-01',
            'clock_in_at'  => Carbon::parse('2026-05-01 08:00:00'),
            'is_late'      => false,
            'total_hours'  => 8.5,
        ]);

        $fresh = $record->fresh();

        $this->assertInstanceOf(Carbon::class, $fresh->clock_in_at);
        $this->assertInstanceOf(Carbon::class, $fresh->date);
        $this->assertIsBool($fresh->is_late);
        $this->assertIsNumeric($fresh->total_hours);
    }

    // 2. isClockedIn() true when clock_in_at set
    public function test_is_clocked_in_true_when_clock_in_at_set(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at = Carbon::now();

        $this->assertTrue($record->isClockedIn());
    }

    // 3. isClockedIn() false when clock_in_at null
    public function test_is_clocked_in_false_when_clock_in_at_null(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at = null;

        $this->assertFalse($record->isClockedIn());
    }

    // 4. isComplete() true when both set
    public function test_is_complete_true_when_both_set(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at  = Carbon::parse('08:00:00');
        $record->clock_out_at = Carbon::parse('17:00:00');

        $this->assertTrue($record->isComplete());
    }

    // 5. isComplete() false when only clock_in set
    public function test_is_complete_false_when_only_clock_in(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at  = Carbon::parse('08:00:00');
        $record->clock_out_at = null;

        $this->assertFalse($record->isComplete());
    }

    // 6. statusLabel() returns 'Absent'
    public function test_status_label_absent(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at  = null;
        $record->clock_out_at = null;

        $this->assertSame('Absent', $record->statusLabel());
    }

    // 7. statusLabel() returns 'Incomplete'
    public function test_status_label_incomplete(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at  = Carbon::parse('08:00:00');
        $record->clock_out_at = null;

        $this->assertSame('Incomplete', $record->statusLabel());
    }

    // 8. statusLabel() returns 'Late'
    public function test_status_label_late(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at  = Carbon::parse('08:30:00');
        $record->clock_out_at = Carbon::parse('17:00:00');
        $record->is_late      = true;

        $this->assertSame('Late', $record->statusLabel());
    }

    // 9. statusLabel() returns 'Present'
    public function test_status_label_present(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_at  = Carbon::parse('07:55:00');
        $record->clock_out_at = Carbon::parse('17:00:00');
        $record->is_late      = false;

        $this->assertSame('Present', $record->statusLabel());
    }

    // 10. clockInMapUrl() returns null when no coords
    public function test_clock_in_map_url_returns_null_when_no_coords(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_lat = null;
        $record->clock_in_lng = null;

        $this->assertNull($record->clockInMapUrl());
    }

    // 11. clockInMapUrl() returns correct Google Maps URL
    public function test_clock_in_map_url_returns_google_maps_url(): void
    {
        $record = new AttendanceRecord();
        $record->clock_in_lat = '3.1412';
        $record->clock_in_lng = '101.6865';

        $url = $record->clockInMapUrl();

        $this->assertNotNull($url);
        $this->assertStringContainsString('3.1412', $url);
        $this->assertStringContainsString('101.6865', $url);
        $this->assertStringContainsString('https://maps.google.com/?q=', $url);
    }

    // 12. clockOutMapUrl() analogous
    public function test_clock_out_map_url_analogous(): void
    {
        $record = new AttendanceRecord();
        $record->clock_out_lat = '3.1412';
        $record->clock_out_lng = '101.6865';

        $url = $record->clockOutMapUrl();

        $this->assertNotNull($url);
        $this->assertStringContainsString('3.1412', $url);
        $this->assertStringContainsString('101.6865', $url);
        $this->assertStringContainsString('https://maps.google.com/?q=', $url);
    }
}
