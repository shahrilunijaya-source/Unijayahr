<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            OrgStructureSeeder::class,
            StaffSeeder::class,
            HandbookPartsSeeder::class,
            ContentFromDraftsSeeder::class,
            PersonalityAssessmentSeeder::class,
            KpiRubricSeeder::class,
            KpiPeriodSeeder::class,
            StaffSuggestionSeeder::class,
            LeaveTypeSeeder::class,
            LeaveBalanceSampleSeeder::class,
        ]);
    }
}
