<?php

namespace Database\Seeders;

use App\Models\StaffSuggestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSuggestionSeeder extends Seeder
{
    public function run(): void
    {
        $yati    = User::where('name', 'Haryati Binti Moktar')->value('id');
        $ahmad   = User::where('name', 'Ahmad Kussairi Bin Sutikno')->value('id');
        $shahril = User::where('name', 'Shahrilnizam Mohamad')->value('id');
        $anis    = User::where('name', 'Anis Eliyani Binti Mohd Ubaidiy')->value('id');

        StaffSuggestion::insert([
            [
                'user_id'            => $yati,
                'is_anonymous'       => false,
                'category'           => 'Operational',
                'title'              => 'Add a coffee machine to the pantry',
                'body'               => 'Would be great if we could have a proper coffee machine in the pantry. Many of us spend on coffee outside when we could have it here for less cost and better productivity.',
                'status'             => 'new',
                'management_response'=> null,
                'responded_at'       => null,
                'responder_id'       => null,
                'created_at'         => now()->subDays(3),
                'updated_at'         => now()->subDays(3),
            ],
            [
                'user_id'            => null,
                'is_anonymous'       => true,
                'category'           => 'HR/People',
                'title'              => 'Review Friday all-hands meeting cadence',
                'body'               => 'The Friday meeting often runs into lunch. Suggest capping it at 45 minutes or shifting to Thursday afternoon so the team can wind down on Friday.',
                'status'             => 'new',
                'management_response'=> null,
                'responded_at'       => null,
                'responder_id'       => null,
                'created_at'         => now()->subDays(5),
                'updated_at'         => now()->subDays(5),
            ],
            [
                'user_id'            => $ahmad,
                'is_anonymous'       => false,
                'category'           => 'Process',
                'title'              => 'Standardise project handover checklist',
                'body'               => 'Each project handover currently happens differently. A shared checklist template would save time and reduce things falling through the cracks when team members transition.',
                'status'             => 'closed',
                'management_response'=> "Great suggestion. We'll work with HR and Project Management to draft a standard checklist by end of Q2. Kussairi, please follow up with Yati to draft a first version.",
                'responded_at'       => now()->subDays(1),
                'responder_id'       => $shahril,
                'created_at'         => now()->subDays(7),
                'updated_at'         => now()->subDays(1),
            ],
            [
                'user_id'            => null,
                'is_anonymous'       => true,
                'category'           => 'Facilities',
                'title'              => 'Fix the air conditioning in the operations room',
                'body'               => 'The AC in the operations room has been inconsistent — sometimes too cold, sometimes not working. Would appreciate a proper servicing.',
                'status'             => 'closed',
                'management_response'=> "Noted. We've scheduled an air conditioning service for next week. Thank you for flagging this — please let HR know if it persists after the service.",
                'responded_at'       => now()->subDays(2),
                'responder_id'       => $anis,
                'created_at'         => now()->subDays(10),
                'updated_at'         => now()->subDays(2),
            ],
        ]);
    }
}
