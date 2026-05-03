<?php
namespace Tests\Feature;

use App\Exceptions\AlreadyClockedInException;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MyAttendancePageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole('staff');
        Storage::fake('local');
    }

    public function test_clock_in_action_creates_record(): void
    {
        $this->actingAs($this->user);

        // Fake the service
        $this->mock(AttendanceService::class)
            ->shouldReceive('clockIn')
            ->once()
            ->andReturn(AttendanceRecord::factory()->create([
                'user_id'    => $this->user->id,
                'date'       => today(),
                'clock_in_at' => now(),
            ]));

        Livewire::test(\App\Filament\Pages\MyAttendancePage::class)
            ->call('clockInAction', 3.1412, 101.6865, 10.0, 'data:image/jpeg;base64,abc')
            ->assertHasNoErrors();
    }

    public function test_double_clock_in_sends_danger_notification(): void
    {
        $this->actingAs($this->user);

        $this->mock(AttendanceService::class)
            ->shouldReceive('clockIn')
            ->once()
            ->andThrow(new AlreadyClockedInException('Already clocked in today.'));

        Livewire::test(\App\Filament\Pages\MyAttendancePage::class)
            ->call('clockInAction', 3.1412, 101.6865, 10.0, 'data:image/jpeg;base64,abc')
            ->assertHasNoErrors()
            ->assertNotified();
    }

    public function test_today_record_computed_returns_todays_row(): void
    {
        $this->actingAs($this->user);

        $record = AttendanceRecord::factory()->create([
            'user_id'    => $this->user->id,
            'date'       => today(),
            'clock_in_at' => now(),
        ]);

        $component = Livewire::test(\App\Filament\Pages\MyAttendancePage::class);

        $this->assertEquals($record->id, $component->instance()->todayRecord?->id);
    }
}
