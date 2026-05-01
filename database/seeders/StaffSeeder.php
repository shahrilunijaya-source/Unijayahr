<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Level;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $levels = Level::pluck('id', 'code');
        $units  = Unit::pluck('id', 'slug');
        $depts  = Department::pluck('id', 'slug');

        // Roster: [callname, full_name, job_title, unit_slug, level_code, role, is_active]
        // Level mapping (old → new 7-band):
        //   intern     → L1   jr_exec → L2   exec      → L3
        //   manager    → L4   sr_mgr  → L5   director  → L6
        $roster = [
            // L6 Sr Management — leadership
            ['shahril',       'Shahrilnizam Mohamad',                          'Director',                'pm',        'L6', 'admin',   true],
            ['suandy',        'Suandy Shamsudin',                              'Director',                null,        'L6', 'staff',   true],

            // L5 Management
            ['haryati',       'Haryati Binti Moktar',                          'Sr Project Manager',      'pm',        'L5', 'manager', true],
            ['hakime',        'Mohd Hakime Bin Md Nasri',                      'Finance Manager',         'accounts',  'L5', 'manager', true],

            // L4 Senior
            ['kussairi',      'Ahmad Kussairi Bin Sutikno',                    'Project Manager',         'pm',        'L4', 'manager', true],
            ['syafiqazwan',   'Mohamad Syafiq Azwan Bin Mustafa',              'Sr Developer',            'developer', 'L4', 'staff',   true],
            ['rubmin',        'Muhamad Rubmin Bin Muhamad',                    'Sr Developer',            'developer', 'L4', 'staff',   true],
            ['hafiz',         'Muhammad Hafiz Ruslan',                         'Sr Developer',            'developer', 'L4', 'staff',   true],

            // L3 Mid
            ['fadzlinaabaziz','Fadzlina Binti Ab Aziz',                        'HR Executive',            'hr',        'L3', 'staff',   false],
            ['nurin',         'Nurin Saffa Binti Safuan',                      'Project Executive',       'pm',        'L3', 'staff',   true],
            ['anasuha',       'Anasuha Binti Mohd Ali',                        'Project Executive',       'pm',        'L3', 'staff',   true],
            ['yaya',          'Nurhidayah Binti Abdul Halim',                  'Project Executive',       'pm',        'L3', 'staff',   true],
            ['amirul',        'Ahmad Amirul Azri Bin Rohandi',                 'Project Executive',       'pm',        'L3', 'staff',   true],
            ['lee',           'Lee Weng Yew',                                  'Developer',               'developer', 'L3', 'staff',   true],
            ['khusalam',      'Tengku Mohamad Khusalam Bin Tuan Mohamad Zaki', 'Developer',               'developer', 'L3', 'staff',   true],
            ['syafiqaffendi', 'Mohd Syafiq Affendi',                           'PHP Developer',           'developer', 'L3', 'staff',   true],
            ['huraidi',       'Huraidi Haidar Bin Adnan',                      'R.E',                     're',        'L3', 'staff',   true],
            ['nabil',         'Nabil Syafiq Bin Azlan',                        'DevOps',                  'devops',    'L3', 'staff',   true],
            ['alya',          'Alya Sabrina Binti Abdul Aziz',                 'Developer',               'developer', 'L3', 'staff',   true],

            // L2 Junior
            ['hidayah',       'Nur Hidayah Suffiah Binti Ramlan',              'Jr Admin Exec',           'admin',     'L2', 'staff',   true],
            ['rohaifiz',      'Mohamad Rohaifiz Bin Abd Karim',                'Jr Developer',            'developer', 'L2', 'staff',   false],
            ['solehin',       'Muhammad Solehin Bin Suhalwi',                  'Jr Developer',            'developer', 'L2', 'staff',   true],
            ['shukri',        'Muhammad Shukri Bin Aman',                      'Jr Developer',            'developer', 'L2', 'staff',   true],
            ['faris',         'Muhammad Faris Akmal Bin Mohd Bustaman',        'Jr Developer',            'developer', 'L2', 'staff',   true],
            ['aniszahidah',   'Nur Anis Zahidah Binti Che Hassan',             'Jr UI/UX',                'uiux',      'L2', 'staff',   true],
            ['syakir',        'Muhammad Syakir Bin Kamarudin',                 'Jr DevOps',               'devops',    'L2', 'staff',   true],
            ['mirza',         'Muhammad Mirza Bin Musa',                       'Jr Tech Doc',             'techdoc',   'L2', 'staff',   true],
            ['emysha',        'Emysha Dhamira Binti Shamsul Azli',             'Jr QA/QC',                'qaqc',      'L2', 'staff',   true],
            ['aminah',        'Siti Nor Aminah Binti Adnan',                   'Admin',                   'admin',     'L2', 'staff',   true],

            // L1 Intern
            ['anis',          'Anis Eliyani Binti Mohd Ubaidiy',               'HR Executive (Intern)',   'hr',        'L1', 'hr',      true],
            ['amirulhakim',   'Nur Amirul Hakim Bin Norsham',                  'Intern Developer',        'developer', 'L1', 'staff',   true],
            ['ikhwan',        'Muhammad Ikhwan Bin Mohd Jamain',               'Intern Developer',        'developer', 'L1', 'staff',   true],
            ['aina',          'Nurul Aina Nabila Binti Ramli',                 'Intern Developer',        'developer', 'L1', 'staff',   true],
        ];

        $credentials = [['Name', 'Email', 'Temp Password', 'Role', 'Status']];
        $byCallname  = [];

        foreach ($roster as [$callname, $name, $jobTitle, $unitSlug, $levelCode, $role, $isActive]) {
            $email    = "{$callname}.unijaya@gmail.com";
            $tempPass = Str::random(12);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'                 => $name,
                    'job_title'            => $jobTitle,
                    'password'             => Hash::make($tempPass),
                    'level_id'             => $levels[$levelCode] ?? null,
                    'unit_id'              => $unitSlug ? ($units[$unitSlug] ?? null) : null,
                    'is_active'            => $isActive,
                    'status'               => $isActive ? 'active' : 'inactive',
                    'must_change_password' => true,
                    'email_verified_at'    => now(),
                ]
            );

            $user->syncRoles([$role]);
            $byCallname[$callname] = $user;

            $credentials[] = [$name, $email, $tempPass, $role, $isActive ? 'active' : 'inactive'];
        }

        // Superior assignments
        $superiorMap = [
            'suandy'        => 'shahril',
            'haryati'       => 'kussairi',
            'hakime'        => 'shahril',
            'kussairi'      => 'shahril',
            'syafiqazwan'   => 'kussairi',
            'rubmin'        => 'kussairi',
            'hafiz'         => 'kussairi',
            'nurin'         => 'kussairi',
            'anasuha'       => 'kussairi',
            'yaya'          => 'kussairi',
            'amirul'        => 'kussairi',
            'lee'           => 'syafiqazwan',
            'khusalam'      => 'syafiqazwan',
            'syafiqaffendi' => 'syafiqazwan',
            'huraidi'       => 'kussairi',
            'nabil'         => 'kussairi',
            'alya'          => 'syafiqazwan',
            'fadzlinaabaziz'=> 'hakime',
            'hidayah'       => 'hakime',
            'aminah'        => 'hakime',
            'rohaifiz'      => 'rubmin',
            'solehin'       => 'rubmin',
            'shukri'        => 'rubmin',
            'faris'         => 'rubmin',
            'aniszahidah'   => 'kussairi',
            'syakir'        => 'nabil',
            'mirza'         => 'kussairi',
            'emysha'        => 'kussairi',
            'anis'          => 'hakime',
            'amirulhakim'   => 'syafiqazwan',
            'ikhwan'        => 'syafiqazwan',
            'aina'          => 'syafiqazwan',
        ];

        foreach ($superiorMap as $callname => $superiorCallname) {
            if (isset($byCallname[$callname], $byCallname[$superiorCallname])) {
                $byCallname[$callname]->update(['superior_id' => $byCallname[$superiorCallname]->id]);
            }
        }

        // Department heads
        Department::where('slug', 'administration')->update(['head_user_id' => $byCallname['hakime']->id]);
        Department::where('slug', 'business')->update(['head_user_id'       => $byCallname['haryati']->id]);
        Department::where('slug', 'operation')->update(['head_user_id'      => $byCallname['kussairi']->id]);

        // Unit head = most-senior member (set via explicit assignment for known units)
        $unitHeads = [
            'hr'        => 'anis',
            'admin'     => 'hidayah',
            'accounts'  => 'hakime',
            'pm'        => 'kussairi',
            'developer' => 'syafiqazwan',
            're'        => 'huraidi',
            'uiux'      => 'aniszahidah',
            'devops'    => 'nabil',
            'techdoc'   => 'mirza',
            'qaqc'      => 'emysha',
        ];
        foreach ($unitHeads as $unitSlug => $callname) {
            if (isset($byCallname[$callname], $units[$unitSlug])) {
                Unit::find($units[$unitSlug])?->update(['head_user_id' => $byCallname[$callname]->id]);
            }
        }

        // Write seed-credentials.csv
        $csv = implode("\n", array_map(
            fn ($row) => implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)),
            $credentials
        ));
        file_put_contents(storage_path('app/seed-credentials.csv'), $csv);

        $active   = collect($roster)->filter(fn ($r) => $r[6])->count();
        $inactive = collect($roster)->filter(fn ($r) => !$r[6])->count();

        $this->command?->info("Seeded {$active} active + {$inactive} inactive staff.");
        $this->command?->info('Credentials → storage/app/seed-credentials.csv');
        $this->command?->warn('NOTE: Magnificent 7 exclusion pending Shahril decision — all 34 staff seeded.');
    }
}
