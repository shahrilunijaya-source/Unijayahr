<?php
namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['corrected_by'] = auth()->id();

        if (!empty($data['clock_in_at']) && !empty($data['clock_out_at'])) {
            $in  = \Carbon\Carbon::parse($data['clock_in_at']);
            $out = \Carbon\Carbon::parse($data['clock_out_at']);
            $data['total_hours'] = round($out->diffInMinutes($in) / 60, 2);
        }

        return $data;
    }
}
