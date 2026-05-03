<?php
namespace App\Filament\Pages;

use App\Models\AttendanceRecord;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Computed;

class TeamAttendancePage extends Page
{
    protected static string $view = 'filament.pages.team-attendance';

    protected static ?string $navigationGroup = 'Attendance';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Team Attendance';

    public string $selectedDate;

    public function mount(): void
    {
        $this->selectedDate = today()->toDateString();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr', 'manager']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Team Attendance';
    }

    #[Computed]
    public function rows()
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'hr'])) {
            $users = User::where('is_active', true)->get();
        } else {
            $users = $user->subordinates()->where('is_active', true)->get();
        }

        $date = $this->selectedDate;
        $records = AttendanceRecord::whereIn('user_id', $users->pluck('id'))
            ->whereDate('date', $date)
            ->get()
            ->groupBy('user_id');

        return $users->each(function (User $u) use ($records) {
            $u->setRelation('attendanceRecords', $records->get($u->id, collect()));
        });
    }
}
