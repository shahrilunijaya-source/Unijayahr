<?php
namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\User;
use Filament\Actions\EditAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['admin', 'hr', 'manager', 'staff'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_non_admin_cannot_access(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->actingAs($staff);
        $this->assertFalse(\App\Filament\Resources\AttendanceResource::canViewAny());
    }

    public function test_admin_can_access(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);
        $this->assertTrue(\App\Filament\Resources\AttendanceResource::canViewAny());
    }

    public function test_hr_can_access(): void
    {
        $hr = User::factory()->create();
        $hr->assignRole('hr');

        $this->actingAs($hr);
        $this->assertTrue(\App\Filament\Resources\AttendanceResource::canViewAny());
    }

    public function test_admin_edit_sets_corrected_by(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $subject = User::factory()->create();
        $record = AttendanceRecord::factory()->create([
            'user_id'    => $subject->id,
            'date'       => today(),
            'clock_in_at' => now()->subHours(8),
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Filament\Resources\AttendanceResource\Pages\EditAttendance::class, [
            'record' => $record->getRouteKey(),
        ])
            ->fillForm([
                'note' => 'Manual correction by admin.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $record->refresh();
        $this->assertEquals($admin->id, $record->corrected_by);
        $this->assertEquals('Manual correction by admin.', $record->note);
    }
}
