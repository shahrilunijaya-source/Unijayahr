<?php

namespace Database\Seeders;

use App\Models\PersonalityAssessment;
use App\Models\PersonalitySection;
use App\Models\PersonalityQuestion;
use Illuminate\Database\Seeder;

class PersonalityAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $assessment = PersonalityAssessment::updateOrCreate(
            ['name' => 'Unijaya Personality Test'],
            [
                'description'    => 'Discover your animal personality type: Rabbit, Tortoise, Fox, or Sloth.',
                'scoring_config' => [
                    'axes'          => ['Rabbit', 'Tortoise', 'Fox', 'Sloth'],
                    'max_per_axis'  => 10,
                    'animal_colors' => [
                        'Rabbit'   => '#f43f5e',
                        'Tortoise' => '#22c55e',
                        'Fox'      => '#f97316',
                        'Sloth'    => '#a855f7',
                    ],
                    'animal_profiles' => [
                        'Rabbit' => [
                            'strengths'    => 'Energetic, social, adaptive, quick learner',
                            'weaknesses'   => 'Impulsive, risk-taking, difficulty with long-term planning',
                            'suitable_for' => 'Leadership, team management, marketing, networking',
                        ],
                        'Tortoise' => [
                            'strengths'    => 'Methodical, detail-oriented, patient, persistent',
                            'weaknesses'   => 'Resistance to change, difficulty multitasking',
                            'suitable_for' => 'Administrative tasks, project management, planning, analysis',
                        ],
                        'Fox' => [
                            'strengths'    => 'Cunning, strategic, creative, flexible',
                            'weaknesses'   => 'Difficulty following rules, tendency to be manipulative',
                            'suitable_for' => 'Strategic planning, negotiation, business development, innovation',
                        ],
                        'Sloth' => [
                            'strengths'    => 'Calm, steady, reliable, good at conserving energy',
                            'weaknesses'   => 'Procrastination, resistance to change, lack of urgency',
                            'suitable_for' => 'Routine tasks, support roles, stability-focused positions',
                        ],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // Clear existing sections/questions to re-seed cleanly
        $assessment->sections()->each(fn ($s) => $s->questions()->delete());
        $assessment->sections()->delete();

        // Section 1 — About You
        $aboutYou = PersonalitySection::create([
            'assessment_id' => $assessment->id,
            'title'         => 'About You',
            'description'   => 'Tell us a little about yourself. Be honest — there are no right or wrong answers.',
            'order'         => 1,
        ]);

        $textQuestions = [
            ['prompt' => 'What is your goal? Please list 3. (Short and simple)', 'meta_key' => 'goals'],
            ['prompt' => 'What are your strengths or skills? Please list 3. (Short and simple)', 'meta_key' => 'strengths'],
            ['prompt' => 'What is your weakness? Please list 3. (Short and simple)', 'meta_key' => 'weaknesses'],
            ['prompt' => 'What are your main interests or hobbies? Please list 3. (Short and simple)', 'meta_key' => 'interests'],
            ['prompt' => 'What is your MBTI type? (e.g. INTJ, ENFP — leave blank if unsure)', 'meta_key' => 'mbti'],
        ];

        foreach ($textQuestions as $i => $q) {
            PersonalityQuestion::create([
                'section_id'      => $aboutYou->id,
                'prompt'          => $q['prompt'],
                'type'            => 'text',
                'options'         => ['meta_key' => $q['meta_key']],
                'scoring_weights' => [],
                'order'           => $i + 1,
            ]);
        }

        // Section 2 — Personality Behavior
        $behavior = PersonalitySection::create([
            'assessment_id' => $assessment->id,
            'title'         => 'Personality Behavior',
            'description'   => 'Choose the answer that best describes you. Go with your gut — pick the one that feels most natural.',
            'order'         => 2,
        ]);

        // Scoring: a=Rabbit, b=Tortoise, c=Fox, d=Sloth
        $w = fn($a, $b, $c, $d) => [
            'a' => ['Rabbit'   => $a],
            'b' => ['Tortoise' => $b],
            'c' => ['Fox'      => $c],
            'd' => ['Sloth'    => $d],
        ];

        $behaviorQuestions = [
            [
                'prompt'  => 'How do you prefer to approach new tasks or challenges?',
                'options' => [
                    ['value' => 'a', 'label' => 'Dive in headfirst and figure things out as you go'],
                    ['value' => 'b', 'label' => 'Take your time to plan and organise before taking action'],
                    ['value' => 'c', 'label' => 'Look for innovative solutions and approach challenges with creativity'],
                    ['value' => 'd', 'label' => 'Evaluate the situation carefully and proceed cautiously'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'In group settings, do you tend to:',
                'options' => [
                    ['value' => 'a', 'label' => 'Take charge and guide the group towards a common goal'],
                    ['value' => 'b', 'label' => 'Listen carefully and contribute thoughtful insights'],
                    ['value' => 'c', 'label' => 'Generate new ideas and inspire others with your enthusiasm'],
                    ['value' => 'd', 'label' => 'Prefer to observe and participate only when necessary'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'How do you handle unexpected changes in your work environment?',
                'options' => [
                    ['value' => 'a', 'label' => 'Adapt quickly and see change as an opportunity for growth'],
                    ['value' => 'b', 'label' => 'Take time to adjust and prefer stability over uncertainty'],
                    ['value' => 'c', 'label' => 'Embrace change and find creative solutions to navigate it'],
                    ['value' => 'd', 'label' => 'Feel uncomfortable with change and prefer familiar routines'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'When working on a team project, do you prefer to:',
                'options' => [
                    ['value' => 'a', 'label' => 'Lead the team and delegate tasks effectively'],
                    ['value' => 'b', 'label' => 'Ensure that all details are carefully considered and executed'],
                    ['value' => 'c', 'label' => 'Generate new ideas and encourage collaboration'],
                    ['value' => 'd', 'label' => 'Work independently and contribute when needed'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'How do you typically approach decision-making?',
                'options' => [
                    ['value' => 'a', 'label' => 'Trust your instincts and make decisions quickly'],
                    ['value' => 'b', 'label' => 'Gather information and weigh the pros and cons before deciding'],
                    ['value' => 'c', 'label' => 'Look for creative solutions and consider multiple perspectives'],
                    ['value' => 'd', 'label' => 'Prefer to avoid making decisions unless absolutely necessary'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'When faced with a setback or failure, how do you react?',
                'options' => [
                    ['value' => 'a', 'label' => 'See it as a learning opportunity and bounce back quickly'],
                    ['value' => 'b', 'label' => 'Analyse what went wrong and adjust your approach accordingly'],
                    ['value' => 'c', 'label' => 'Look for alternative solutions and remain optimistic'],
                    ['value' => 'd', 'label' => 'Feel discouraged and may need time to recover'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'How do you prefer to communicate with others?',
                'options' => [
                    ['value' => 'a', 'label' => 'Direct and assertive, focusing on achieving results'],
                    ['value' => 'b', 'label' => 'Clear and concise, providing detailed explanations'],
                    ['value' => 'c', 'label' => 'Engaging and persuasive, using storytelling and emotion'],
                    ['value' => 'd', 'label' => 'Reserved and thoughtful, preferring one-on-one conversations'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'What motivates you in your work?',
                'options' => [
                    ['value' => 'a', 'label' => 'Achieving goals and seeing tangible results'],
                    ['value' => 'b', 'label' => 'Stability and security in your role'],
                    ['value' => 'c', 'label' => 'Challenging yourself and exploring new opportunities'],
                    ['value' => 'd', 'label' => 'Maintaining a healthy work-life balance'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'How do you prefer to spend your free time?',
                'options' => [
                    ['value' => 'a', 'label' => 'Engaging in social activities and spending time with friends'],
                    ['value' => 'b', 'label' => 'Relaxing at home and pursuing hobbies or interests'],
                    ['value' => 'c', 'label' => 'Exploring new experiences and seeking adventure'],
                    ['value' => 'd', 'label' => 'Taking it easy and enjoying quiet, solitary activities'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
            [
                'prompt'  => 'What best describes your approach to goal-setting?',
                'options' => [
                    ['value' => 'a', 'label' => 'Setting ambitious goals and striving to achieve them quickly'],
                    ['value' => 'b', 'label' => 'Setting realistic goals and working steadily towards them'],
                    ['value' => 'c', 'label' => 'Setting flexible goals that allow for adaptability and innovation'],
                    ['value' => 'd', 'label' => 'Preferring to live in the moment without specific goals'],
                ],
                'weights' => $w(1, 1, 1, 1),
            ],
        ];

        foreach ($behaviorQuestions as $i => $q) {
            PersonalityQuestion::create([
                'section_id'      => $behavior->id,
                'prompt'          => $q['prompt'],
                'type'            => 'single',
                'options'         => $q['options'],
                'scoring_weights' => $q['weights'],
                'order'           => $i + 1,
            ]);
        }

        $this->command?->info('Personality assessment seeded: 2 sections, 15 questions (5 text + 10 behavioral).');
    }
}
