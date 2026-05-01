<?php

namespace App\Filament\Resources\EmployeeProfileResource\Pages;

use App\Filament\Resources\EmployeeProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeProfile extends EditRecord
{
    protected static string $resource = EmployeeProfileResource::class;

    protected function getHeaderActions(): array { return []; }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $record->load(['employeeProfile', 'bankDetail']);

        $data['employeeProfile'] = $record->employeeProfile?->toArray() ?? [];
        $data['bankDetail']      = $record->bankDetail?->toArray() ?? [];

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $profileData = $data['employeeProfile'] ?? [];
        $bankData    = $data['bankDetail'] ?? [];
        unset($data['employeeProfile'], $data['bankDetail']);

        // User record fields (name, job_title) are read-only on this form — no update needed
        // Profile data is saved via updateOrCreate below

        if ($profileData) {
            $record->employeeProfile()->updateOrCreate(['user_id' => $record->id], $profileData);
        }
        if ($bankData) {
            $record->bankDetail()->updateOrCreate(['user_id' => $record->id], $bankData);
        }

        return $record;
    }
}
