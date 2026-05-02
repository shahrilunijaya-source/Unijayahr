<?php

namespace App\Filament\Pages;

use App\Models\LeaveRequest;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class LeaveApprovalsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Leave Approvals';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.leave-approvals';

    public string $rejectNote = '';

    #[Locked]
    public ?int $rejectingId = null;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'hr', 'manager']);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'hr', 'manager']);
    }

    #[Computed]
    public function pendingRequests(): Collection
    {
        $user  = auth()->user();
        $query = LeaveRequest::query()
            ->where('status', 'pending')
            ->with(['applicant', 'leaveType']);

        if ($user->hasAnyRole(['admin', 'hr'])) {
            // see all
        } else {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        return $query->oldest('created_at')->get();
    }

    public function approve(int $id): void
    {
        $user = auth()->user();

        // Atomic: only update if still pending AND (admin/hr OR belongs to subordinate)
        $query = LeaveRequest::where('id', $id)->where('status', 'pending');
        if (!$user->hasAnyRole(['admin', 'hr'])) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        $affected = $query->update([
            'status'         => 'approved',
            'manager_id'     => $user->id,
            'hr_notified_at' => now(),
        ]);

        if ($affected === 0) {
            Notification::make()->title('Request not found or already actioned.')->danger()->send();
            return;
        }

        // Reload for notification body
        $request = LeaveRequest::with(['applicant', 'leaveType'])->find($id);

        // Notify applicant
        Notification::make()
            ->title('Leave request approved')
            ->body($request->leaveType->name . ' · ' . $request->start_date->format('d M') . ' – ' . $request->end_date->format('d M Y'))
            ->success()
            ->sendToDatabase($request->applicant);

        // Notify HR/admin (skip self)
        $hrUsers = User::role(['admin', 'hr'])->where('id', '!=', $user->id)->get();
        foreach ($hrUsers as $hrUser) {
            Notification::make()
                ->title($request->applicant->name . '\'s leave approved')
                ->body($request->leaveType->name . ' · ' . $request->total_days . ' day(s)')
                ->info()
                ->sendToDatabase($hrUser);
        }

        Notification::make()->title('Leave approved.')->success()->send();
    }

    public function openRejectModal(int $id): void
    {
        $this->rejectingId = $id;
        $this->rejectNote  = '';
        $this->dispatch('open-reject-modal');
    }

    public function confirmReject(): void
    {
        if (!$this->rejectingId) return;

        $user = auth()->user();

        $query = LeaveRequest::where('id', $this->rejectingId)->where('status', 'pending');
        if (!$user->hasAnyRole(['admin', 'hr'])) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        $this->validate(['rejectNote' => 'nullable|string|max:1000']);

        $affected = $query->update([
            'status'       => 'rejected',
            'manager_id'   => $user->id,
            'manager_note' => $this->rejectNote ?: null,
        ]);

        if ($affected === 0) {
            Notification::make()->title('Request not found or already actioned.')->danger()->send();
            $this->rejectingId = null;
            $this->rejectNote  = '';
            return;
        }

        $request = LeaveRequest::with(['applicant', 'leaveType'])->find($this->rejectingId);

        Notification::make()
            ->title('Leave request rejected')
            ->body($request->leaveType->name . ($this->rejectNote ? ': ' . $this->rejectNote : ''))
            ->danger()
            ->sendToDatabase($request->applicant);

        Notification::make()->title('Leave rejected.')->warning()->send();

        $this->rejectingId = null;
        $this->rejectNote  = '';
    }

    public function cancelReject(): void
    {
        $this->rejectingId = null;
        $this->rejectNote  = '';
    }
}
