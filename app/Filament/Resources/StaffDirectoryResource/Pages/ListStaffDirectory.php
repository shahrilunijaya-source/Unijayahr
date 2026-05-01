<?php

namespace App\Filament\Resources\StaffDirectoryResource\Pages;

use App\Filament\Resources\StaffDirectoryResource;
use Filament\Resources\Pages\ListRecords;

class ListStaffDirectory extends ListRecords
{
    protected static string $resource = StaffDirectoryResource::class;

    protected function getHeaderActions(): array { return []; }
}
