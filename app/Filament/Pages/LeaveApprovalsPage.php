<?php

namespace App\Filament\Pages;

use App\Models\LeaveRequest;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LeaveApprovalsPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Leave Approvals';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.leave-approvals';

    public string $rejectNote = '';
    public ?int $rejectingId = null;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'hr', 'manager']);
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'hr', 'manager']);
    }

    public function getPendingRequests()
    {
        $user  = auth()->user();
        $query = LeaveRequest::query()
            ->where('status', 'pending')
            ->with(['applicant', 'leaveType']);

        if ($user->hasAnyRole(['admin', 'hr'])) {
            // see all
        } else {
            // manager: only own subordinates
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        return $query->oldest('created_at')->get();
    }

    public function approve(int $id): void
    {
        $request = $this->findApprovable($id);
        if (!$request) return;

        $request->update([
            'status'         => 'approved',
            'manager_id'     => auth()->id(),
            'hr_notified_at' => now(),
        ]);

        // Notify applicant
        Notification::make()
            ->title('Leave request approved')
            ->body($request->leaveType->name . ' · ' . $request->start_date->format('d M') . ' – ' . $request->end_date->format('d M Y'))
            ->success()
            ->sendToDatabase($request->applicant);

        // Notify HR (if approver is not HR/admin themselves)
        $hrUsers = User::role(['admin', 'hr'])->where('id', '!=', auth()->id())->get();
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

        $request = $this->findApprovable($this->rejectingId);
        if (!$request) {
            $this->rejectingId = null;
            return;
        }

        $request->update([
            'status'       => 'rejected',
            'manager_id'   => auth()->id(),
            'manager_note' => $this->rejectNote ?: null,
        ]);

        // Notify applicant
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

    private function findApprovable(int $id): ?LeaveRequest
    {
        $user  = auth()->user();
        $query = LeaveRequest::where('id', $id)->where('status', 'pending');

        if (!$user->hasAnyRole(['admin', 'hr'])) {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->whereIn('user_id', $subordinateIds);
        }

        $request = $query->first();

        if (!$request) {
            Notification::make()->title('Request not found or already actioned.')->danger()->send();
            return null;
        }

        return $request;
    }
}
