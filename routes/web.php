<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/app'));

Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('attendance/photo/{record}/{which}', \App\Http\Controllers\AttendancePhotoController::class)
    ->middleware('auth')
    ->name('attendance.photo');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/documents/download/{id}', function ($id) {
        $doc = \App\Models\EmployeeDocument::findOrFail($id);
        $user = auth()->user();

        if (! $user->hasAnyRole(['admin', 'hr']) && $doc->user_id !== $user->id) {
            abort(403);
        }

        if (! \Storage::disk('local')->exists($doc->file_path)) {
            abort(404);
        }

        return \Storage::disk('local')->download($doc->file_path, $doc->display_label);
    })->name('documents.download');
});
