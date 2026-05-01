<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Level;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrgStructureSeeder extends Seeder
{
    public function run(): void
    {
        // 7-band levels per Comp Framework v1.0
        $levels = [
            ['code' => 'L1', 'order' => 1, 'name' => 'Intern',        'salary_min' => 800,    'salary_mid' => 1200,  'salary_max' => 1800],
            ['code' => 'L2', 'order' => 2, 'name' => 'Junior',        'salary_min' => 2800,   'salary_mid' => 3300,  'salary_max' => 4000],
            ['code' => 'L3', 'order' => 3, 'name' => 'Mid',           'salary_min' => 3500,   'salary_mid' => 4500,  'salary_max' => 5500],
            ['code' => 'L4', 'order' => 4, 'name' => 'Senior',        'salary_min' => 5500,   'salary_mid' => 6500,  'salary_max' => 7500],
            ['code' => 'L5', 'order' => 5, 'name' => 'Management',    'salary_min' => 6500,   'salary_mid' => 8000,  'salary_max' => 10000],
            ['code' => 'L6', 'order' => 6, 'name' => 'Sr Management', 'salary_min' => 10000,  'salary_mid' => 13000, 'salary_max' => 16000],
            ['code' => 'L7', 'order' => 7, 'name' => 'Executive',     'salary_min' => null,   'salary_mid' => null,  'salary_max' => null],
        ];

        foreach ($levels as $data) {
            Level::firstOrCreate(['code' => $data['code']], $data);
        }

        // 3 departments (per canonical Google Sheet)
        $depts = [
            ['name' => 'Administration', 'slug' => 'administration', 'description' => 'Admin, HR, Accounts'],
            ['name' => 'Business',       'slug' => 'business',       'description' => 'Marketing and Sales'],
            ['name' => 'Operation',      'slug' => 'operation',      'description' => 'Project delivery and engineering'],
        ];

        $departments = [];
        foreach ($depts as $data) {
            $departments[$data['slug']] = Department::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // 13 units: [slug, name, department_slug, order]
        $unitDefs = [
            ['admin',      'Admin',              'administration', 1],
            ['hr',         'HR',                 'administration', 2],
            ['accounts',   'Accounts',           'administration', 3],
            ['marketing',  'Marketing',          'business',       1],
            ['sales',      'Sales',              'business',       2],
            ['pm',         'Project Management', 'operation',      1],
            ['analyst',    'Analyst',            'operation',      2],
            ['developer',  'Developer',          'operation',      3],
            ['re',         'RE',                 'operation',      4],
            ['uiux',       'UI/UX',              'operation',      5],
            ['devops',     'Dev/Ops',            'operation',      6],
            ['techdoc',    'Tech Doc',           'operation',      7],
            ['qaqc',       'QA/QC',              'operation',      8],
        ];

        foreach ($unitDefs as [$slug, $name, $deptSlug, $order]) {
            Unit::firstOrCreate(
                ['slug' => $slug, 'department_id' => $departments[$deptSlug]->id],
                ['name' => $name, 'order' => $order]
            );
        }

        $this->command?->info('Seeded 7 levels, 3 departments, 13 units.');
    }
}
