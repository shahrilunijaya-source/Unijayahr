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

        // orderBy('id') ensures deterministic selection across DB engines
        User::where('is_active', true)->orderBy('id')->take(5)->get()->each(function (User $user) use ($annual, $mc, $year) {
            LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $annual->id, 'year' => $year],
                // Sample seeder uses 12 days — production HR must set per-staff based on tenure
                ['allocated_days' => 12, 'carried_over' => 0]
            );
            LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $mc->id, 'year' => $year],
                // 14 days = EA 1955 standard outpatient MC entitlement (5+ yrs service)
                // max_days_per_year=22 on the type includes hospitalisation leave cap
                ['allocated_days' => 14, 'carried_over' => 0]
            );
        });
    }
}
