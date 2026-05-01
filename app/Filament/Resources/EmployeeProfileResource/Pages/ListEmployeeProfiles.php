<?php

namespace App\Filament\Resources\EmployeeProfileResource\Pages;

use App\Filament\Resources\EmployeeProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeProfiles extends ListRecords
{
    protected static string $resource = EmployeeProfileResource::class;
}
