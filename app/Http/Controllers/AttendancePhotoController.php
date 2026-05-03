<?php
namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendancePhotoController extends Controller
{
    public function __invoke(Request $request, AttendanceRecord $record, string $which): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();

        $isOwn        = $user->id === $record->user_id;
        $isAdminOrHr  = $user->hasAnyRole(['admin', 'hr']);
        $isSubordinate = $user->subordinates()->where('id', $record->user_id)->exists();

        if (!$isOwn && !$isAdminOrHr && !$isSubordinate) {
            abort(403);
        }

        $path = match ($which) {
            'in'  => $record->clock_in_photo_path,
            'out' => $record->clock_out_photo_path,
            default => abort(404),
        };

        if ($path === null || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path);
    }
}
