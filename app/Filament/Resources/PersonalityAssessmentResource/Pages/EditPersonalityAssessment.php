<?php

namespace App\Filament\Resources\PersonalityAssessmentResource\Pages;

use App\Filament\Resources\PersonalityAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalityAssessment extends EditRecord
{
    protected static string $resource = PersonalityAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
