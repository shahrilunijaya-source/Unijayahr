<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'shahril@unijaya.com'],
            [
                'name'       => 'Shahril',
                'password'   => bcrypt('admin1234'),
                'is_active'  => true,
                'status'     => 'active',
                'join_date'  => '2020-01-01',
            ]
        );

        $admin->syncRoles(['admin']);
    }
}
