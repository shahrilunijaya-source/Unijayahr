<?php
namespace App\Filament\Pages;

use App\Exceptions\AlreadyClockedInException;
use App\Exceptions\AlreadyClockedOutException;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Computed;

class MyAttendancePage extends Page
{
    protected static string $view = 'filament.pages.my-attendance';

    protected static ?string $navigationGroup = 'Account';
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'My Attendance';

    public function getTitle(): string|Htmlable
    {
        return 'My Attendance';
    }

    #[Computed]
    public function todayRecord(): ?AttendanceRecord
    {
        return AttendanceRecord::forUser(auth()->id())
            ->forDate(today())
            ->first();
    }

    #[Computed]
    public function recentRecords()
    {
        return AttendanceRecord::forUser(auth()->id())
            ->between(today()->subDays(29), today())
            ->orderByDesc('date')
            ->get();
    }

    public function clockInAction(float $lat, float $lng, ?float $accuracy, string $photo): void
    {
        try {
            app(AttendanceService::class)->clockIn(auth()->user(), $lat, $lng, $accuracy, $photo);
            Notification::make()->title('Clocked in successfully.')->success()->send();
        } catch (AlreadyClockedInException $e) {
            Notification::make()->title('Already clocked in today.')->danger()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Clock-in failed: ' . $e->getMessage())->danger()->send();
        }

        unset($this->todayRecord);
        unset($this->recentRecords);
    }

    public function clockOutAction(float $lat, float $lng, ?float $accuracy, string $photo): void
    {
        try {
            app(AttendanceService::class)->clockOut(auth()->user(), $lat, $lng, $accuracy, $photo);
            Notification::make()->title('Clocked out successfully.')->success()->send();
        } catch (AlreadyClockedOutException $e) {
            Notification::make()->title('Already clocked out today.')->danger()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Clock-out failed: ' . $e->getMessage())->danger()->send();
        }

        unset($this->todayRecord);
        unset($this->recentRecords);
    }
}
