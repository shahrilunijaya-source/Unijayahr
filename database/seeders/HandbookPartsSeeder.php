<?php

namespace Database\Seeders;

use App\Models\HandbookPart;
use Illuminate\Database\Seeder;

class HandbookPartsSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            ['A', 'About Unijaya',       'Who we are, how we work, and the values behind it.',           '#061b31', 'heroicon-o-building-office',          1,
             '<p>Unijaya Resources Sdn Bhd is a holding company operating corporate services alongside two petrol stations — Shell S2 and Petron TL. This handbook covers the full Unijaya group of ~59 staff.</p>'],
            ['B', 'Employment Terms',    'Offer letters, probation, notice, confirmation.',               '#533afd', 'heroicon-o-document-text',             2,
             '<p>Your offer letter sets out the core terms. This Part explains how probation works, when you&rsquo;re confirmed, and what happens if either side needs to end the relationship.</p>'],
            ['C', 'Leave',               'Annual, medical, maternity, paternity, compassionate, emergency.', '#665efd', 'heroicon-o-calendar-days',          3,
             '<p>Unijaya follows the Employment Act 1955 for statutory leave. Annual leave scales with years of service.</p>'],
            ['D', 'Attendance',          'Hours, late arrivals, overtime, work from home.',               '#4434d4', 'heroicon-o-clock',                     4,
             '<p>Standard office hours are 10am&ndash;7pm Monday to Friday. Stations run shift rosters set by station managers.</p>'],
            ['E', 'Benefits',            'Medical, hibah, training, allowances.',                         '#533afd', 'heroicon-o-heart',                     5,
             '<p>Beyond statutory benefits, Unijaya offers annual Hibah, training sponsorship, and medical reimbursement.</p>'],
            ['F', 'Performance',         'KPI, salary review, promotion.',                                '#2e2b8c', 'heroicon-o-chart-bar',                  6,
             '<p>We run quarterly KPI reviews. Your self-rating plus your manager&rsquo;s rating set your overall score each quarter.</p>'],
            ['G', 'Conduct',             'Code of conduct, NDA, IP, conflict of interest, anti-corruption.', '#ea2261', 'heroicon-o-shield-check',           7,
             '<p>We expect honesty, respect, and care in how we treat colleagues, customers, and suppliers.</p>'],
            ['H', 'Protective Policies', 'ASH, grievance, data protection, whistleblower.',              '#ea2261', 'heroicon-o-shield-exclamation',         8,
             '<p>These policies exist so you have clear, safe paths when something goes wrong. No retaliation. All complaints investigated.</p>'],
            ['I', 'Discipline',          'Warnings, hearings, sanctions, appeals.',                       '#f96bee', 'heroicon-o-exclamation-triangle',       9,
             '<p>We believe in progressive discipline. Report issues early. Escalate through the proper channels.</p>'],
            ['J', 'Health & Safety',     'OSH, emergencies, cyber security, equipment.',                  '#9b6829', 'heroicon-o-fire',                      10,
             '<p>Everyone is responsible for health and safety at work. Report hazards. Attend the annual fire drill.</p>'],
            ['K', 'Ending Employment',   'Resignation, notice, clearance, EA Form.',                      '#15be53', 'heroicon-o-arrow-right-on-rectangle',  11,
             '<p>When you leave, the process needs to happen properly. Follow the 5-step clearance checklist.</p>'],
            ['L', 'Internship Program',  'Terms, conversion, stipend.',                                   '#15be53', 'heroicon-o-academic-cap',              12,
             '<p>We run a structured internship program alongside permanent hiring.</p>'],
            ['M', 'Policy Governance',   'Who owns policies, how amendments are made.',                   '#061b31', 'heroicon-o-cog-6-tooth',               13,
             '<p>Policies evolve. This Part explains who can propose changes and how amendments get approved.</p>'],
        ];

        foreach ($parts as [$code, $title, $tagline, $accent, $icon, $order, $intro]) {
            HandbookPart::firstOrCreate(
                ['code' => $code],
                [
                    'title'        => $title,
                    'tagline'      => $tagline,
                    'accent_color' => $accent,
                    'icon'         => $icon,
                    'order'        => $order,
                    'intro_body'   => $intro,
                    'is_published' => true,
                ]
            );
        }
    }
}
