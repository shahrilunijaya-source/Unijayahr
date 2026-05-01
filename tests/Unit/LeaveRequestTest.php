<?php
namespace Tests\Unit;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    public function test_count_weekdays_single_day(): void
    {
        $monday = Carbon::parse('2026-03-02');
        $this->assertEquals(1.0, LeaveRequest::countWeekdays($monday, $monday));
    }

    public function test_count_weekdays_full_week(): void
    {
        $monday = Carbon::parse('2026-03-02');
        $friday = Carbon::parse('2026-03-06');
        $this->assertEquals(5.0, LeaveRequest::countWeekdays($monday, $friday));
    }

    public function test_count_weekdays_excludes_weekend(): void
    {
        $friday = Carbon::parse('2026-03-06');
        $monday = Carbon::parse('2026-03-09');
        // Fri + Mon = 2 weekdays (Sat/Sun excluded)
        $this->assertEquals(2.0, LeaveRequest::countWeekdays($friday, $monday));
    }

    public function test_count_weekdays_starting_on_saturday(): void
    {
        $saturday = Carbon::parse('2026-03-07');
        $monday   = Carbon::parse('2026-03-09');
        // Sat excluded, Sun excluded, Mon counted = 1
        $this->assertEquals(1.0, LeaveRequest::countWeekdays($saturday, $monday));
    }

    public function test_can_be_cancelled_when_pending(): void
    {
        $request = new LeaveRequest(['status' => 'pending']);
        $this->assertTrue($request->canBeCancelled());
    }

    public function test_cannot_be_cancelled_when_approved(): void
    {
        $request = new LeaveRequest(['status' => 'approved']);
        $this->assertFalse($request->canBeCancelled());
    }

    public function test_is_pending(): void
    {
        $request = new LeaveRequest(['status' => 'pending']);
        $this->assertTrue($request->isPending());
        $this->assertFalse($request->isApproved());
    }

    public function test_is_rejected(): void
    {
        $request = new LeaveRequest(['status' => 'rejected']);
        $this->assertTrue($request->isRejected());
        $this->assertFalse($request->isPending());
    }

    public function test_is_cancelled(): void
    {
        $request = new LeaveRequest(['status' => 'cancelled']);
        $this->assertTrue($request->isCancelled());
        $this->assertFalse($request->isPending());
    }
}
