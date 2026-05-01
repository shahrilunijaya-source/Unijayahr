<?php

namespace Database\Seeders;

use App\Models\KpiPeriod;
use Illuminate\Database\Seeder;

class KpiPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $periods = [
            ['label' => '2025-Q4', 'start_date' => '2025-10-01', 'end_date' => '2025-12-31', 'status' => 'closed',  'opened_at' => '2025-10-01', 'closed_at' => '2026-01-15'],
            ['label' => '2026-Q1', 'start_date' => '2026-01-01', 'end_date' => '2026-03-31', 'status' => 'closed',  'opened_at' => '2026-01-01', 'closed_at' => '2026-04-15'],
            ['label' => '2026-Q2', 'start_date' => '2026-04-01', 'end_date' => '2026-06-30', 'status' => 'open',    'opened_at' => '2026-04-01'],
            ['label' => '2026-Q3', 'start_date' => '2026-07-01', 'end_date' => '2026-09-30', 'status' => 'draft'],
        ];

        foreach ($periods as $data) {
            KpiPeriod::firstOrCreate(['label' => $data['label']], $data);
        }

        $this->command?->info('Seeded 4 KPI periods (all 4 statuses covered for dev/QA).');
    }
}
