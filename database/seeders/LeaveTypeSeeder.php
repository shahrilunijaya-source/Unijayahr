<?php
namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Annual Leave',       'code' => 'annual',        'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Medical Leave',       'code' => 'mc',            'is_paid' => true,  'requires_document' => true,  'max_days_per_year' => 22],
            ['name' => 'Emergency Leave',     'code' => 'emergency',     'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 3],
            ['name' => 'Maternity Leave',     'code' => 'maternity',     'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 98],
            ['name' => 'Paternity Leave',     'code' => 'paternity',     'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 7],
            ['name' => 'Unpaid Leave',        'code' => 'unpaid',        'is_paid' => false, 'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Replacement Leave',   'code' => 'replacement',   'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Study Leave',         'code' => 'study',         'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => null],
            ['name' => 'Compassionate Leave', 'code' => 'compassionate', 'is_paid' => true,  'requires_document' => false, 'max_days_per_year' => 3],
        ];

        foreach ($types as $type) {
            LeaveType::firstOrCreate(['code' => $type['code']], $type + ['is_active' => true]);
        }
    }
}
