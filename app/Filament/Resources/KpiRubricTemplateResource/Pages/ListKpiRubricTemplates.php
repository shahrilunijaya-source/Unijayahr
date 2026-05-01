<?php

namespace App\Filament\Resources\KpiRubricTemplateResource\Pages;

use App\Filament\Resources\KpiRubricTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKpiRubricTemplates extends ListRecords
{
    protected static string $resource = KpiRubricTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
