<?php

namespace Tests\Feature;

use App\Filament\Pages\LeaveApprovalsPage;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveApprovalsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin', 'hr', 'manager', 'staff'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    private function makeUser(array $attrs = []): User
    {
        return User::factory()->create(array_merge(['is_active' => true], $attrs));
    }

    private function makeLeaveType(): LeaveType
    {
        return LeaveType::factory()->create(['is_active' => true]);
    }

    private function makePendingRequest(User $applicant, LeaveType $type): LeaveRequest
    {
        return LeaveRequest::create([
            'user_id'       => $applicant->id,
            'leave_type_id' => $type->id,
            'start_date'    => '2026-07-01',
            'end_date'      => '2026-07-03',
            'total_days'    => 3.0,
            'status'        => 'pending',
        ]);
    }

    // 1. Manager sees only requests from their subordinates
    public function test_manager_sees_only_subordinate_requests(): void
    {
        $manager = $this->makeUser();
        $manager->assignRole('manager');

        $sub1 = $this->makeUser(['superior_id' => $manager->id]);
        $sub2 = $this->makeUser(['superior_id' => $manager->id]);
        $unrelated = $this->makeUser();

        $type = $this->makeLeaveType();

        $req1 = $this->makePendingRequest($sub1, $type);
        $req2 = $this->makePendingRequest($sub2, $type);
        $req3 = $this->makePendingRequest($unrelated, $type);

        $component = Livewire::actingAs($manager)->test(LeaveApprovalsPage::class);
        $pending = $component->instance()->getPendingRequests();

        $ids = $pending->pluck('id')->all();

        $this->assertCount(2, $pending);
        $this->assertContains($req1->id, $ids);
        $this->assertContains($req2->id, $ids);
        $this->assertNotContains($req3->id, $ids);
    }

    // 2. HR sees all pending requests
    public function test_hr_sees_all_pending_requests(): void
    {
        $hr = $this->makeUser();
        $hr->assignRole('hr');

        $type = $this->makeLeaveType();

        $u1 = $this->makeUser();
        $u2 = $this->makeUser();
        $u3 = $this->makeUser();

        $this->makePendingRequest($u1, $type);
        $this->makePendingRequest($u2, $type);
        $this->makePendingRequest($u3, $type);

        $component = Livewire::actingAs($hr)->test(LeaveApprovalsPage::class);
        $pending = $component->instance()->getPendingRequests();

        $this->assertCount(3, $pending);
    }

    // 3. Manager can approve a subordinate's request
    public function test_manager_can_approve_request(): void
    {
        $manager = $this->makeUser();
        $manager->assignRole('manager');

        $sub = $this->makeUser(['superior_id' => $manager->id]);
        $type = $this->makeLeaveType();
        $req = $this->makePendingRequest($sub, $type);

        Livewire::actingAs($manager)
            ->test(LeaveApprovalsPage::class)
            ->call('approve', $req->id);

        $req->refresh();

        $this->assertEquals('approved', $req->status);
        $this->assertEquals($manager->id, $req->manager_id);
        $this->assertNotNull($req->hr_notified_at);
    }

    // 4. Manager can reject with a note
    public function test_manager_can_reject_with_note(): void
    {
        $manager = $this->makeUser();
        $manager->assignRole('manager');

        $sub = $this->makeUser(['superior_id' => $manager->id]);
        $type = $this->makeLeaveType();
        $req = $this->makePendingRequest($sub, $type);

        Livewire::actingAs($manager)
            ->test(LeaveApprovalsPage::class)
            ->set('rejectingId', $req->id)
            ->set('rejectNote', 'Peak season, cannot be granted.')
            ->call('confirmReject');

        $req->refresh();

        $this->assertEquals('rejected', $req->status);
        $this->assertEquals($manager->id, $req->manager_id);
        $this->assertEquals('Peak season, cannot be granted.', $req->manager_note);
    }

    // 5. Manager cannot approve a request outside their team
    public function test_manager_cannot_approve_request_outside_team(): void
    {
        $manager = $this->makeUser();
        $manager->assignRole('manager');

        $unrelated = $this->makeUser(); // no superior_id pointing to manager
        $type = $this->makeLeaveType();
        $req = $this->makePendingRequest($unrelated, $type);

        Livewire::actingAs($manager)
            ->test(LeaveApprovalsPage::class)
            ->call('approve', $req->id);

        $req->refresh();

        $this->assertEquals('pending', $req->status);
    }

    // 6. Staff without manager/hr/admin role cannot access the page
    public function test_staff_cannot_access_page(): void
    {
        $staff = $this->makeUser();
        $staff->assignRole('staff');

        $this->actingAs($staff);

        $this->assertFalse(LeaveApprovalsPage::canAccess());
    }
}
