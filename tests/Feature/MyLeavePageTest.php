<?php

namespace Tests\Feature;

use App\Filament\Pages\MyLeavePage;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyLeavePageTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create(['is_active' => true]);
    }

    private function makeLeaveType(array $attrs = []): LeaveType
    {
        return LeaveType::create(array_merge([
            'name'              => 'Annual Leave',
            'code'              => 'AL',
            'is_paid'           => true,
            'requires_document' => false,
            'max_days_per_year' => 14.0,
            'is_active'         => true,
        ], $attrs));
    }

    // 1. Staff can view the My Leave page
    public function test_staff_can_view_my_leave_page(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $response = $this->get('/app/my-leave-page');

        $response->assertStatus(200);
    }

    // 2. Balance cards show on page
    public function test_balance_cards_show_on_page(): void
    {
        $user = $this->makeUser();
        $type = $this->makeLeaveType();

        LeaveBalance::create([
            'user_id'       => $user->id,
            'leave_type_id' => $type->id,
            'year'          => now()->year,
            'allocated_days'=> 14.0,
            'carried_over'  => 0.0,
        ]);

        Livewire::actingAs($user)
            ->test(MyLeavePage::class)
            ->assertSee('Annual Leave')
            ->assertSee('14.0');
    }

    // 3. Staff can submit a leave request
    public function test_staff_can_submit_leave_request(): void
    {
        $user = $this->makeUser();
        $type = $this->makeLeaveType();

        Livewire::actingAs($user)
            ->test(MyLeavePage::class)
            ->fillForm([
                'leave_type_id' => $type->id,
                'start_date'    => '2026-06-02',
                'end_date'      => '2026-06-04',
                'reason'        => 'Family matter',
            ])
            ->call('apply');

        $this->assertDatabaseHas('leave_requests', [
            'user_id'       => $user->id,
            'leave_type_id' => $type->id,
            'status'        => 'pending',
        ]);

        $request = LeaveRequest::where('user_id', $user->id)->first();
        $this->assertGreaterThan(0, (float) $request->total_days);
    }

    // 4. Total days excludes weekends (Mon–Fri = 5 days)
    public function test_total_days_excludes_weekends(): void
    {
        $user = $this->makeUser();
        $type = $this->makeLeaveType();

        // 2026-06-01 is Monday, 2026-06-05 is Friday — 5 weekdays
        Livewire::actingAs($user)
            ->test(MyLeavePage::class)
            ->fillForm([
                'leave_type_id' => $type->id,
                'start_date'    => '2026-06-01',
                'end_date'      => '2026-06-05',
                'reason'        => null,
            ])
            ->call('apply');

        $request = LeaveRequest::where('user_id', $user->id)->first();
        $this->assertEquals(5.0, (float) $request->total_days);
    }

    // 5. Staff can cancel a pending request
    public function test_staff_can_cancel_pending_request(): void
    {
        $user = $this->makeUser();
        $type = $this->makeLeaveType();

        $leaveRequest = LeaveRequest::create([
            'user_id'       => $user->id,
            'leave_type_id' => $type->id,
            'start_date'    => '2026-07-01',
            'end_date'      => '2026-07-02',
            'total_days'    => 2.0,
            'status'        => 'pending',
        ]);

        Livewire::actingAs($user)
            ->test(MyLeavePage::class)
            ->call('cancelRequest', $leaveRequest->id);

        $this->assertDatabaseHas('leave_requests', [
            'id'     => $leaveRequest->id,
            'status' => 'cancelled',
        ]);
    }

    // 6. Cannot cancel an approved request
    public function test_cannot_cancel_another_users_request(): void
    {
        $user1 = \App\Models\User::factory()->create(['is_active' => true]);
        $user2 = \App\Models\User::factory()->create(['is_active' => true]);
        $leaveType = \App\Models\LeaveType::factory()->create(['is_active' => true]);

        $request = \App\Models\LeaveRequest::create([
            'user_id'       => $user2->id,
            'leave_type_id' => $leaveType->id,
            'start_date'    => now()->addDays(3)->toDateString(),
            'end_date'      => now()->addDays(4)->toDateString(),
            'total_days'    => 2,
            'status'        => 'pending',
        ]);

        Livewire::actingAs($user1)
            ->test(\App\Filament\Pages\MyLeavePage::class)
            ->call('cancelRequest', $request->id)
            ->assertNotified(); // got "Request not found" danger notification

        $this->assertDatabaseHas('leave_requests', ['id' => $request->id, 'status' => 'pending']);
    }

    // 7. Cannot cancel an approved request
    public function test_cannot_cancel_approved_request(): void
    {
        $user = $this->makeUser();
        $type = $this->makeLeaveType();

        $leaveRequest = LeaveRequest::create([
            'user_id'       => $user->id,
            'leave_type_id' => $type->id,
            'start_date'    => '2026-07-01',
            'end_date'      => '2026-07-02',
            'total_days'    => 2.0,
            'status'        => 'approved',
        ]);

        Livewire::actingAs($user)
            ->test(MyLeavePage::class)
            ->call('cancelRequest', $leaveRequest->id)
            ->assertNotified();

        $this->assertDatabaseHas('leave_requests', [
            'id'     => $leaveRequest->id,
            'status' => 'approved',
        ]);
    }
}
