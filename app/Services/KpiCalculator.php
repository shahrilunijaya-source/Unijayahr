<?php

namespace App\Services;

use App\Models\KpiReview;

class KpiCalculator
{
    public function calculateSelf(KpiReview $review): void
    {
        $scores = $review->scores()->whereNotNull('self_score')->get();

        $total = $scores->sum(
            fn ($s) => $s->max_score_snapshot > 0 ? ($s->self_score / $s->max_score_snapshot) * $s->item_weight_snapshot : 0
        );

        $review->update([
            'self_total'        => round($total, 2),
            'self_submitted_at' => now(),
            'status'            => 'pending_superior',
            'pending_role'      => 'reviewer',
        ]);
    }

    public function calculateSuperior(KpiReview $review): void
    {
        $scores = $review->scores()->whereNotNull('superior_score')->get();

        $superiorTotal = $scores->sum(
            fn ($s) => $s->max_score_snapshot > 0 ? ($s->superior_score / $s->max_score_snapshot) * $s->item_weight_snapshot : 0
        );

        $period     = $review->period;
        $finalTotal = (($review->self_total ?? 0) * $period->self_weight)
                    + ($superiorTotal * $period->superior_weight);

        $review->update([
            'superior_total'        => round($superiorTotal, 2),
            'final_total'           => round($finalTotal, 2),
            'superior_submitted_at' => now(),
            'finalized_at'          => now(),
            'status'                => 'finalized',
            'pending_role'          => 'nobody',
        ]);
    }
}
