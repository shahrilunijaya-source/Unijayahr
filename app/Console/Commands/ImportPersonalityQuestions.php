<?php

namespace App\Console\Commands;

use App\Models\PersonalityAssessment;
use App\Models\PersonalityQuestion;
use App\Models\PersonalitySection;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportPersonalityQuestions extends Command
{
    protected $signature = 'personality:import {--csv= : Path to Google Form CSV export} {--assessment-id=1 : Assessment ID}';
    protected $description = 'Import personality test questions from Google Form CSV export';

    public function handle(): int
    {
        $csvPath      = $this->option('csv');
        $assessmentId = (int) $this->option('assessment-id');

        if (! $csvPath || ! file_exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");
            $this->info('Usage: php artisan personality:import --csv=/path/to/form-export.csv --assessment-id=1');
            $this->info('');
            $this->info('Expected CSV columns:');
            $this->info('  section_title, question_prompt, type (single|scale|text), option_a_label, option_a_D, option_a_I, option_a_S, option_a_C, ...');
            return self::FAILURE;
        }

        $assessment = PersonalityAssessment::find($assessmentId);
        if (! $assessment) {
            $this->error("Assessment {$assessmentId} not found.");
            return self::FAILURE;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_map('trim', array_shift($csv));

        $sections = [];
        $imported = 0;

        foreach ($csv as $i => $row) {
            if (count($row) < 2) continue;
            $data = array_combine($headers, array_pad($row, count($headers), ''));

            $sectionTitle = trim($data['section_title'] ?? 'Section 1');
            $prompt       = trim($data['question_prompt'] ?? '');
            $type         = trim($data['type'] ?? 'single');

            if (empty($prompt)) continue;

            // Get or create section
            if (! isset($sections[$sectionTitle])) {
                $sections[$sectionTitle] = PersonalitySection::firstOrCreate(
                    ['assessment_id' => $assessmentId, 'title' => $sectionTitle],
                    ['order' => count($sections) + 1]
                );
            }

            $section = $sections[$sectionTitle];

            // Build options + scoring_weights from CSV
            $options = [];
            $weights = [];
            $axes    = $assessment->scoring_config['axes'] ?? ['D', 'I', 'S', 'C'];

            foreach (range('a', 'z') as $letter) {
                $labelKey = "option_{$letter}_label";
                if (empty($data[$labelKey])) break;

                $options[] = ['value' => $letter, 'label' => trim($data[$labelKey])];
                $axisWeights = [];
                foreach ($axes as $axis) {
                    $key = "option_{$letter}_" . strtoupper($axis);
                    if (! empty($data[$key])) {
                        $axisWeights[strtoupper($axis)] = (float) $data[$key];
                    }
                }
                if ($axisWeights) {
                    $weights[$letter] = $axisWeights;
                }
            }

            PersonalityQuestion::create([
                'section_id'      => $section->id,
                'prompt'          => $prompt,
                'type'            => $type,
                'options'         => $options ?: null,
                'scoring_weights' => $weights ?: null,
                'order'           => $i + 1,
            ]);

            $imported++;
        }

        $this->info("Imported {$imported} questions across " . count($sections) . " sections.");
        return self::SUCCESS;
    }
}
