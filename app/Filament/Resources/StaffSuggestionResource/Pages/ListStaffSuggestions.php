<?php

namespace App\Filament\Resources\StaffSuggestionResource\Pages;

use App\Filament\Resources\StaffSuggestionResource;
use Filament\Resources\Pages\ListRecords;

class ListStaffSuggestions extends ListRecords
{
    protected static string $resource = StaffSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
