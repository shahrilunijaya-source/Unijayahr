<?php
namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveBalanceSampleSeeder extends Seeder
{
    public function run(): void
    {
        $year = 2026;
        $annual = LeaveType::where('code', 'annual')->first();
        $mc = LeaveType::where('code', 'mc')->first();

        if (! $annual || ! $mc) return;

        User::where('is_active', true)->take(5)->get()->each(function (User $user) use ($annual, $mc, $year) {
            LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $annual->id, 'year' => $year],
                ['allocated_days' => 12, 'carried_over' => 0]
            );
            LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $mc->id, 'year' => $year],
                ['allocated_days' => 14, 'carried_over' => 0]
            );
        });
    }
}
