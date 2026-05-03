<?php
namespace Tests\Feature;

use App\Exceptions\AlreadyClockedInException;
use App\Exceptions\AlreadyClockedOutException;
use App\Exceptions\NotClockedInException;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AttendanceService::class);
        $this->user = User::factory()->create([
            'work_start_time' => '10:00:00',
            'work_end_time'   => '19:00:00',
        ]);
        Storage::fake('local');
    }

    public function test_clock_in_creates_record(): void
    {
        $photo = $this->makePhotoBase64();
        $record = $this->service->clockIn($this->user, 3.1412, 101.6865, 10.0, $photo);

        $this->assertInstanceOf(AttendanceRecord::class, $record);
        $this->assertNotNull($record->clock_in_at);
        $this->assertEquals($this->user->id, $record->user_id);
        $this->assertNotNull($record->clock_in_photo_path);
        Storage::disk('local')->assertExists($record->clock_in_photo_path);
    }

    public function test_clock_in_twice_throws(): void
    {
        $photo = $this->makePhotoBase64();
        $this->service->clockIn($this->user, 3.1412, 101.6865, 10.0, $photo);

        $this->expectException(AlreadyClockedInException::class);
        $this->service->clockIn($this->user, 3.1412, 101.6865, 10.0, $photo);
    }

    public function test_clock_out_records_total_hours(): void
    {
        $photo = $this->makePhotoBase64();
        $this->service->clockIn($this->user, 3.1412, 101.6865, 10.0, $photo);

        $record = $this->service->clockOut($this->user, 3.1412, 101.6865, 10.0, $photo);

        $this->assertNotNull($record->clock_out_at);
        $this->assertNotNull($record->total_hours);
    }

    public function test_clock_out_without_clock_in_throws(): void
    {
        $photo = $this->makePhotoBase64();

        $this->expectException(NotClockedInException::class);
        $this->service->clockOut($this->user, 3.1412, 101.6865, 10.0, $photo);
    }

    public function test_double_clock_out_throws(): void
    {
        $photo = $this->makePhotoBase64();
        $this->service->clockIn($this->user, 3.1412, 101.6865, 10.0, $photo);
        $this->service->clockOut($this->user, 3.1412, 101.6865, 10.0, $photo);

        $this->expectException(AlreadyClockedOutException::class);
        $this->service->clockOut($this->user, 3.1412, 101.6865, 10.0, $photo);
    }

    public function test_late_flag_set_when_beyond_grace(): void
    {
        // Work starts 10:00, clock in at 10:10 (>5 min grace) → is_late
        $this->user->update(['work_start_time' => '10:00:00']);

        $photo = $this->makePhotoBase64();
        // Travel time so clock_in_at will be 10:10
        \Carbon\Carbon::setTestNow(today()->setTime(10, 10));
        $record = $this->service->clockIn($this->user, 3.1412, 101.6865, 10.0, $photo);
        \Carbon\Carbon::setTestNow();

        $this->assertTrue($record->is_late);
        $this->assertEquals(10, $record->late_minutes);
    }

    public function test_on_time_no_late_flag(): void
    {
        $this->user->update(['work_start_time' => '10:00:00']);

        $photo = $this->makePhotoBase64();
        \Carbon\Carbon::setTestNow(today()->setTime(10, 3)); // 3 min — within 5-min grace
        $record = $this->service->clockIn($this->user, 3.1412, 101.6865, 10.0, $photo);
        \Carbon\Carbon::setTestNow();

        $this->assertFalse($record->is_late);
    }

    private function makePhotoBase64(): string
    {
        // 1×1 red JPEG — valid GD-generated image, confirmed passes getimagesizefromstring
        $b64 = '/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcg' .
               'SlBFRyB2ODApLCBxdWFsaXR5ID0gODUK/9sAQwAFAwQEBAMFBAQEBQUFBgcMCAcHBwcPCwsJDBEP' .
               'EhIRDxERExYcFxMUGhURERghGBodHR8fHxMXIiQiHiQcHh8e/9sAQwEFBQUHBgcOCAgOHhQRFB4e' .
               'Hh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4e/8AAEQgAAQAB' .
               'AwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQE' .
               'AAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5' .
               'OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqy' .
               's7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A8sooor86P7LP/9k=';
        return 'data:image/jpeg;base64,' . $b64;
    }
}
