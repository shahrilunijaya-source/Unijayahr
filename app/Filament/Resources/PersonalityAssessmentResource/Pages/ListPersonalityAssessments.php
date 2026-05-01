<?php

namespace App\Filament\Resources\PersonalityAssessmentResource\Pages;

use App\Filament\Resources\PersonalityAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonalityAssessments extends ListRecords
{
    protected static string $resource = PersonalityAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
