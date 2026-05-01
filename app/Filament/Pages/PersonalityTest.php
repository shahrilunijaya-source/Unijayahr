<?php

namespace App\Filament\Pages;

use App\Models\PersonalityAnswer;
use App\Models\PersonalityAssessment;
use App\Models\PersonalityResponse;
use App\Services\PersonalityScorer;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PersonalityTest extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Personality Test';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.personality-test';

    public ?int $currentStep = 0;
    public array $answers = [];
    public bool $submitted = false;
    public ?PersonalityResponse $existingResponse = null;

    public function mount(): void
    {
        $assessment = PersonalityAssessment::where('is_active', true)->first();
        if ($assessment) {
            $this->existingResponse = PersonalityResponse::where('user_id', auth()->id())
                ->where('assessment_id', $assessment->id)
                ->whereNotNull('submitted_at')
                ->first();
        }
    }

    public function getAssessment(): ?PersonalityAssessment
    {
        return PersonalityAssessment::with('sections.questions')->where('is_active', true)->first();
    }

    public function nextStep(): void
    {
        $this->currentStep++;
    }

    public function prevStep(): void
    {
        $this->currentStep = max(0, $this->currentStep - 1);
    }

    public function submit(): void
    {
        $assessment = $this->getAssessment();
        if (! $assessment) return;

        $response = PersonalityResponse::firstOrCreate([
            'user_id'       => auth()->id(),
            'assessment_id' => $assessment->id,
        ]);

        foreach ($this->answers as $questionId => $value) {
            PersonalityAnswer::updateOrCreate(
                ['response_id' => $response->id, 'question_id' => $questionId],
                ['value' => is_array($value) ? $value : [$value]]
            );
        }

        app(PersonalityScorer::class)->score($response);

        $this->submitted         = true;
        $this->existingResponse  = $response->fresh();

        Notification::make()->title('Personality test submitted!')->success()->send();
    }
}
