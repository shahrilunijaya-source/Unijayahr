<?php
namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamAttendancePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['admin', 'hr', 'manager', 'staff'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_staff_cannot_access(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->actingAs($staff);
        $this->assertFalse(\App\Filament\Pages\TeamAttendancePage::canAccess());
    }

    public function test_admin_can_access(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);
        $this->assertTrue(\App\Filament\Pages\TeamAttendancePage::canAccess());
    }

    public function test_manager_sees_only_subordinates(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $sub   = User::factory()->create(['superior_id' => $manager->id]);
        $other = User::factory()->create();

        AttendanceRecord::factory()->create(['user_id' => $sub->id,   'date' => today()]);
        AttendanceRecord::factory()->create(['user_id' => $other->id, 'date' => today()]);

        $this->actingAs($manager);
        $component = Livewire::test(\App\Filament\Pages\TeamAttendancePage::class);

        $rows = $component->instance()->rows;

        $userIds = $rows->pluck('id')->toArray();
        $this->assertContains($sub->id,   $userIds);
        $this->assertNotContains($other->id, $userIds);
    }

    public function test_admin_sees_all_active_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $userA = User::factory()->create(['is_active' => true]);
        $userB = User::factory()->create(['is_active' => true]);
        $inactive = User::factory()->create(['is_active' => false]);

        $this->actingAs($admin);
        $component = Livewire::test(\App\Filament\Pages\TeamAttendancePage::class);

        $userIds = $component->instance()->rows->pluck('id')->toArray();
        $this->assertContains($userA->id, $userIds);
        $this->assertContains($userB->id, $userIds);
        $this->assertNotContains($inactive->id, $userIds);
    }

    public function test_date_filter_changes_loaded_records(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create(['is_active' => true]);
        $yesterday = today()->subDay()->toDateString();
        AttendanceRecord::factory()->create(['user_id' => $user->id, 'date' => $yesterday, 'clock_in_at' => now()->subDay()]);

        $this->actingAs($admin);
        $component = Livewire::test(\App\Filament\Pages\TeamAttendancePage::class)
            ->set('selectedDate', $yesterday);

        $rows = $component->instance()->rows;
        $userRow = $rows->firstWhere('id', $user->id);
        $this->assertNotNull($userRow);
        $this->assertNotNull($userRow->attendanceRecords->first()?->clock_in_at);
    }
}
