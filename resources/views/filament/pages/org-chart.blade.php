<x-filament-panels::page>
    <div class="mb-4 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            {{ $mode === 'department' ? 'Department → Unit → Staff' : 'Reporting tree (superior chains)' }}
        </div>
        <x-filament::button color="gray" size="sm" wire:click="toggleMode">
            @if($mode === 'department')
                <x-heroicon-o-arrows-up-down class="h-4 w-4 mr-1 inline" />Switch to Reporting Tree
            @else
                <x-heroicon-o-building-office class="h-4 w-4 mr-1 inline" />Switch to Dept View
            @endif
        </x-filament::button>
    </div>

    @if($mode === 'department')
        {{-- Department tree --}}
        <div class="space-y-6">
            @foreach($this->getDepartments() as $dept)
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-2 rounded-t-xl border-b border-gray-100 bg-navy px-5 py-3 dark:border-gray-700">
                        <x-heroicon-o-building-office class="h-5 w-5 text-white/70" />
                        <span class="font-semibold text-white tracking-wide">{{ $dept->name }}</span>
                    </div>

                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach($dept->units as $unit)
                            @if($unit->users->count())
                                <div class="px-5 py-3">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="rounded bg-stripe-purple/10 px-2 py-0.5 text-xs font-semibold text-stripe-purple">{{ $unit->name }}</span>
                                    </div>
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach($unit->users->sortByDesc(fn ($u) => $u->level?->order) as $user)
                                            <a href="{{ \App\Filament\Resources\StaffDirectoryResource::getUrl('view', ['record' => $user]) }}"
                                               class="flex items-center gap-3 rounded-lg border border-gray-100 px-3 py-2 text-sm transition hover:border-stripe-purple hover:bg-stripe-purple/5 dark:border-gray-700">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=533afd&background=e5e3ff&size=32"
                                                     class="h-8 w-8 rounded-full flex-shrink-0" alt="{{ $user->name }}">
                                                <div class="min-w-0">
                                                    <p class="truncate font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                                    @if($user->level)
                                                        <p class="text-xs text-gray-400">{{ $user->level->code }} — {{ $user->level->name }}</p>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Reporting tree --}}
        @php $tree = $this->getReportingTree() @endphp
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            @include('filament.partials.org-tree-node', ['nodes' => $tree])
        </div>
    @endif
</x-filament-panels::page>
