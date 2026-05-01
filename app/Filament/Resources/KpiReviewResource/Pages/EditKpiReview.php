<?php

namespace App\Filament\Resources\KpiReviewResource\Pages;

use App\Filament\Resources\KpiReviewResource;
use App\Models\KpiReviewScore;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKpiReview extends EditRecord
{
    protected static string $resource = KpiReviewResource::class;

    private array $pendingScores = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $scores = [];
        foreach ($this->record->scores as $score) {
            $scores[$score->id] = [
                'self_score'       => $score->self_score,
                'self_comment'     => $score->self_comment,
                'superior_score'   => $score->superior_score,
                'superior_comment' => $score->superior_comment,
            ];
        }
        $data['scores'] = $scores;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingScores = $data['scores'] ?? [];
        unset($data['scores']);
        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->pendingScores as $scoreId => $fields) {
            $toUpdate = array_filter($fields, fn ($v) => $v !== null);
            if (! empty($toUpdate)) {
                KpiReviewScore::where('id', $scoreId)
                    ->where('review_id', $this->record->id)
                    ->update($toUpdate);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
