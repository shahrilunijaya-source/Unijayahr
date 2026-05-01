<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model           = LeaveRequest::class;
    protected static ?string $navigationIcon  = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationGroup = 'Leave';
    protected static ?string $navigationLabel = 'All Leave Requests';
    protected static ?int    $navigationSort  = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function canCreate(): bool { return false; }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Request')->schema([
                Infolists\Components\TextEntry::make('applicant.name')->label('Staff'),
                Infolists\Components\TextEntry::make('leaveType.name')->label('Leave Type'),
                Infolists\Components\TextEntry::make('start_date')->date(),
                Infolists\Components\TextEntry::make('end_date')->date(),
                Infolists\Components\TextEntry::make('total_days')->label('Days'),
                Infolists\Components\TextEntry::make('status')->badge()
                    ->color(fn ($state) => match($state) {
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'cancelled' => 'gray',
                        default     => 'warning',
                    }),
                Infolists\Components\TextEntry::make('reason')->columnSpanFull(),
            ])->columns(3),

            Infolists\Components\Section::make('Decision')
                ->hidden(fn ($record) => $record->isPending())
                ->schema([
                    Infolists\Components\TextEntry::make('manager.name')->label('Decided by'),
                    Infolists\Components\TextEntry::make('manager_note')->label('Note')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('applicant.name')->label('Staff')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('leaveType.name')->label('Type'),
            Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('end_date')->date(),
            Tables\Columns\TextColumn::make('total_days')->label('Days'),
            Tables\Columns\TextColumn::make('status')->badge()
                ->color(fn ($state) => match($state) {
                    'approved'  => 'success',
                    'rejected'  => 'danger',
                    'cancelled' => 'gray',
                    default     => 'warning',
                }),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending'   => 'Pending',
                    'approved'  => 'Approved',
                    'rejected'  => 'Rejected',
                    'cancelled' => 'Cancelled',
                ]),
            Tables\Filters\SelectFilter::make('leave_type_id')
                ->label('Leave Type')
                ->relationship('leaveType', 'name'),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => $record->isPending())
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'status'         => 'approved',
                        'manager_id'     => auth()->id(),
                        'hr_notified_at' => now(),
                    ]);
                }),
            Tables\Actions\Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) => $record->isPending())
                ->form([
                    Forms\Components\Textarea::make('manager_note')
                        ->label('Reason')
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    $record->update([
                        'status'       => 'rejected',
                        'manager_id'   => auth()->id(),
                        'manager_note' => $data['manager_note'],
                    ]);
                }),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'view'  => Pages\ViewLeaveRequest::route('/{record}'),
        ];
    }
}
