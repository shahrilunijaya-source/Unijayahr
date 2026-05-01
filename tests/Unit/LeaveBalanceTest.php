<?php
namespace Tests\Unit;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveBalanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private LeaveType $leaveType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->leaveType = LeaveType::create([
            'name' => 'Annual Leave', 'code' => 'annual',
            'is_paid' => true, 'requires_document' => false, 'is_active' => true,
        ]);
    }

    public function test_remaining_days_with_no_requests(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 12.0,
            'carried_over'   => 2.0,
        ]);

        $this->assertEquals(14.0, $balance->remainingDays());
    }

    public function test_remaining_days_deducts_approved_requests(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 12.0,
            'carried_over'   => 0.0,
        ]);

        LeaveRequest::create([
            'user_id'       => $this->user->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => '2026-03-02',
            'end_date'      => '2026-03-04',
            'total_days'    => 3.0,
            'status'        => 'approved',
        ]);

        $this->assertEquals(9.0, $balance->remainingDays());
    }

    public function test_pending_requests_do_not_reduce_balance(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 12.0,
            'carried_over'   => 0.0,
        ]);

        LeaveRequest::create([
            'user_id'       => $this->user->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => '2026-03-02',
            'end_date'      => '2026-03-04',
            'total_days'    => 3.0,
            'status'        => 'pending',
        ]);

        $this->assertEquals(12.0, $balance->remainingDays());
    }

    public function test_remaining_days_never_negative(): void
    {
        $balance = LeaveBalance::create([
            'user_id'        => $this->user->id,
            'leave_type_id'  => $this->leaveType->id,
            'year'           => 2026,
            'allocated_days' => 2.0,
            'carried_over'   => 0.0,
        ]);

        LeaveRequest::create([
            'user_id'       => $this->user->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => '2026-03-02',
            'end_date'      => '2026-03-06',
            'total_days'    => 5.0,
            'status'        => 'approved',
        ]);

        $this->assertEquals(0.0, $balance->remainingDays());
    }
}
