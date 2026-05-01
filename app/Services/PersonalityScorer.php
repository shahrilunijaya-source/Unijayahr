<?php

namespace App\Services;

use App\Models\PersonalityResponse;

class PersonalityScorer
{
    public function score(PersonalityResponse $response): PersonalityResponse
    {
        $config = $response->assessment->scoring_config ?? [];
        $axes   = $config['axes'] ?? [];
        $max    = $config['max_per_axis'] ?? 10;

        $rawScores = array_fill_keys($axes, 0);
        $textData  = [];

        $answers = $response->answers()->with('question')->get();

        foreach ($answers as $answer) {
            $question = $answer->question;
            $value    = is_array($answer->value) ? ($answer->value[0] ?? null) : $answer->value;

            if ($question->type === 'text') {
                $metaKey = $question->options['meta_key'] ?? null;
                if ($metaKey && $value !== null) {
                    $textData[$metaKey] = $value;
                }
                continue;
            }

            if ($value === null) continue;

            $weights     = $question->scoring_weights ?? [];
            $axisWeights = $weights[$value] ?? [];

            foreach ($axisWeights as $axis => $points) {
                if (array_key_exists($axis, $rawScores)) {
                    $rawScores[$axis] += (float) $points;
                }
            }
        }

        // Percentages (max_per_axis behavioral questions, each worth 1 point)
        $scores = [];
        foreach ($rawScores as $axis => $score) {
            $scores[$axis] = $max > 0 ? (int) round(($score / $max) * 100) : 0;
        }

        $dominant = ! empty($rawScores) ? array_keys($rawScores, max($rawScores))[0] : null;

        $response->update([
            'raw_scores'      => $rawScores,
            'computed_result' => array_merge(
                ['scores' => $scores, 'dominant' => $dominant],
                $textData
            ),
            'submitted_at' => now(),
        ]);

        return $response;
    }
}
