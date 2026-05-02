<?php

namespace Tests\Feature;

use App\Filament\Pages\MyProfile;
use App\Models\EmployeeDocument;
use App\Models\EmployeeProfile;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyProfileEnhancedTest extends TestCase
{
    use RefreshDatabase;

    public function test_personal_info_prefills_from_profile(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        EmployeeProfile::create([
            'user_id'        => $user->id,
            'nationality'    => 'Singaporean',
            'gender'         => 'male',
            'marital_status' => 'single',
        ]);

        Livewire::actingAs($user)
            ->test(MyProfile::class)
            ->assertSet('data.nationality', 'Singaporean')
            ->assertSet('data.gender', 'male');
    }

    public function test_save_creates_employee_profile(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(MyProfile::class)
            ->set('data.nationality', 'Malaysian')
            ->set('data.gender', 'female')
            ->set('data.marital_status', 'single')
            ->call('save');

        $this->assertDatabaseHas('employee_profiles', [
            'user_id'     => $user->id,
            'nationality' => 'Malaysian',
            'gender'      => 'female',
        ]);
    }

    public function test_save_updates_existing_employee_profile(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        EmployeeProfile::create(['user_id' => $user->id, 'nationality' => 'Malaysian', 'gender' => 'male']);

        Livewire::actingAs($user)
            ->test(MyProfile::class)
            ->set('data.gender', 'female')
            ->call('save');

        $this->assertDatabaseHas('employee_profiles', ['user_id' => $user->id, 'gender' => 'female']);
        $this->assertDatabaseCount('employee_profiles', 1); // no duplicate
    }
}
