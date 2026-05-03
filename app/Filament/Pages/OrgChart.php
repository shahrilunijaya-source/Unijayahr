<?php

namespace App\Filament\Pages;

use App\Models\Department;
use App\Models\User;
use Filament\Pages\Page;

class OrgChart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static ?string $navigationLabel = 'Org Chart';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.org-chart';

    public string $mode = 'department';

    public function toggleMode(): void
    {
        $this->mode = $this->mode === 'department' ? 'reporting' : 'department';
    }

    public function getDepartments()
    {
        $depts = Department::with(['units' => function ($q) {
            $q->orderBy('order')->with(['users' => function ($u) {
                $u->where('is_active', true)->with('level')->orderBy('name');
            }]);
        }])->get();

        foreach ($depts as $dept) {
            foreach ($dept->units as $unit) {
                $unit->setRelation('users',
                    $unit->users->sortByDesc(fn ($u) => $u->level?->order ?? 0)->values()
                );
            }
        }

        return $depts;
    }

    public function getReportingTree(): array
    {
        $allUsers = User::where('is_active', true)
            ->with('level')
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        return $this->buildTree($allUsers, null, 0);
    }

    private function buildTree($allUsers, ?int $parentId, int $depth): array
    {
        return $allUsers
            ->where('superior_id', $parentId)
            ->map(fn ($user) => [
                'user'     => $user,
                'depth'    => $depth,
                'children' => $this->buildTree($allUsers, $user->id, $depth + 1),
            ])
            ->values()
            ->all();
    }
}
