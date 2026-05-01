<?php

namespace Database\Seeders;

use App\Models\KpiRubricItem;
use App\Models\KpiRubricTemplate;
use App\Models\Level;
use Illuminate\Database\Seeder;

class KpiRubricSeeder extends Seeder
{
    public function run(): void
    {
        $genericItems = [
            ['criterion' => 'Quality of Work',  'weight' => 34, 'order' => 1],
            ['criterion' => 'Communication',     'weight' => 33, 'order' => 2],
            ['criterion' => 'Ownership',         'weight' => 33, 'order' => 3],
        ];

        foreach (Level::all() as $level) {
            $template = KpiRubricTemplate::firstOrCreate(
                ['level_id' => $level->id, 'version' => 1],
                [
                    'name'           => "{$level->name} KPI Rubric v1",
                    'is_active'      => true,
                    'effective_from' => '2026-01-01',
                ]
            );

            foreach ($genericItems as $item) {
                KpiRubricItem::firstOrCreate(
                    ['template_id' => $template->id, 'criterion' => $item['criterion']],
                    ['weight' => $item['weight'], 'max_score' => 5, 'order' => $item['order']]
                );
            }
        }

        $this->command?->info('KPI rubric placeholder created for all 7 levels (3 generic items each).');
        $this->command?->warn('TODO: Replace with real rubric per level when user provides raw content.');
    }
}
