<?php

namespace App\Filament\Pages;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MyLeavePage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'My Leave';
    protected static ?string $navigationGroup = 'Account';
    protected static ?int    $navigationSort  = 11;
    protected static string  $view            = 'filament.pages.my-leave';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Select::make('leave_type_id')
                ->label('Leave Type')
                ->options(LeaveType::active()->pluck('name', 'id'))
                ->required()
                ->live(),

            Forms\Components\DatePicker::make('start_date')
                ->label('Start Date')
                ->required()
                ->minDate(now()->toDateString()),

            Forms\Components\DatePicker::make('end_date')
                ->label('End Date')
                ->required()
                ->minDate(now()->toDateString())
                ->afterOrEqual('start_date'),

            Forms\Components\Textarea::make('reason')
                ->label('Reason')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('document')
                ->label('Supporting Document (e.g. MC cert)')
                ->disk('local')
                ->directory('leave-documents/' . auth()->id())
                ->visibility('private')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->maxSize(10240)
                ->nullable()
                ->columnSpanFull()
                ->visible(function (Forms\Get $get) {
                    $typeId = $get('leave_type_id');
                    if (!$typeId) return false;
                    return (bool) LeaveType::find($typeId)?->requires_document;
                })
                ->required(fn (\Filament\Forms\Get $get): bool =>
                    (bool) \App\Models\LeaveType::find($get('leave_type_id'))?->requires_document
                ),
        ])->columns(2);
    }

    public function apply(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        \App\Models\LeaveType::active()->findOrFail($data['leave_type_id']);

        $start     = Carbon::parse($data['start_date']);
        $end       = Carbon::parse($data['end_date']);
        $totalDays = LeaveRequest::countWeekdays($start, $end);

        $request = LeaveRequest::create([
            'user_id'       => $user->id,
            'leave_type_id' => $data['leave_type_id'],
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'total_days'    => $totalDays,
            'reason'        => $data['reason'] ?? null,
            'status'        => 'pending',
            'document_path' => $data['document'] ?? null,
        ]);

        // Notify manager (superior)
        if ($user->superior) {
            Notification::make()
                ->title('New leave request from ' . $user->name)
                ->body($request->leaveType->name . ' · ' . $totalDays . ' day(s) · ' . $start->format('d M') . ' – ' . $end->format('d M Y'))
                ->warning()
                ->sendToDatabase($user->superior);
        }

        Notification::make()->title('Leave request submitted.')->success()->send();
        $this->form->fill();
    }

    public function getBalances()
    {
        $year = now()->year;
        return LeaveBalance::where('user_id', auth()->id())
            ->where('year', $year)
            ->whereHas('leaveType', fn ($q) => $q->where('is_active', true))
            ->with('leaveType')
            ->get();
    }

    public function getHistory()
    {
        return LeaveRequest::where('user_id', auth()->id())
            ->with('leaveType')
            ->latest()
            ->limit(50)
            ->get();
    }

    public function cancelRequest(int $id): void
    {
        $request = \App\Models\LeaveRequest::where('user_id', auth()->id())->find($id);

        if (!$request) {
            Notification::make()->title('Request not found.')->danger()->send();
            return;
        }

        if (!$request->canBeCancelled()) {
            Notification::make()->title('Request cannot be cancelled.')->danger()->send();
            return;
        }

        $request->update(['status' => 'cancelled']);
        Notification::make()->title('Leave request cancelled.')->success()->send();
    }
}
