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
        return Department::with(['units' => function ($q) {
            $q->orderBy('order')->with(['users' => function ($u) {
                $u->where('is_active', true)
                  ->with('level')
                  ->orderByRaw('(SELECT `order` FROM levels WHERE levels.id = users.level_id) DESC')
                  ->orderBy('name');
            }]);
        }])->get();
    }

    public function getReportingTree(): array
    {
        $roots = User::where('is_active', true)
            ->whereNull('superior_id')
            ->with('level')
            ->orderBy('name')
            ->get();

        return $this->buildTree($roots);
    }

    private function buildTree($users, int $depth = 0): array
    {
        $tree = [];
        foreach ($users as $user) {
            $subordinates = User::where('superior_id', $user->id)
                ->where('is_active', true)
                ->with('level')
                ->orderBy('name')
                ->get();

            $tree[] = [
                'user'     => $user,
                'depth'    => $depth,
                'children' => $subordinates->isNotEmpty() ? $this->buildTree($subordinates, $depth + 1) : [],
            ];
        }
        return $tree;
    }
}
