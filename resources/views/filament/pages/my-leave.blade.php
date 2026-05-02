<x-filament-panels::page>

    {{-- 1. Balance cards --}}
    @php $balances = $this->getBalances(); @endphp
    @if($balances->isNotEmpty())
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 mb-6">
            @foreach($balances as $balance)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $balance->leaveType->name }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($balance->remainingDays(), 1) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">of {{ number_format($balance->totalAllocated(), 1) }} days remaining</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- 2. Apply form --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Apply for Leave</h2>
        <form wire:submit="apply">
            {{ $this->form }}
            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" color="primary">Submit request</x-filament::button>
            </div>
        </form>
    </div>

    {{-- 3. Leave history --}}
    @php $history = $this->getHistory(); @endphp
    @if($history->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Leave History</h2>
            <div class="space-y-3">
                @foreach($history as $req)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $req->leaveType->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $req->start_date->format('d M Y') }} – {{ $req->end_date->format('d M Y') }}
                                        ({{ $req->total_days }} day(s))
                                    </span>
                                </div>
                                @if($req->reason)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $req->reason }}</p>
                                @endif
                                @if($req->manager_note)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">Manager note: {{ $req->manager_note }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span @class([
                                    'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400' => $req->isPending(),
                                    'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' => $req->isApproved(),
                                    'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400' => $req->isRejected(),
                                    'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' => $req->isCancelled(),
                                ])>{{ ucfirst($req->status) }}</span>
                                @if($req->canBeCancelled())
                                    <button
                                        wire:click="cancelRequest({{ $req->id }})"
                                        wire:confirm="Cancel this leave request?"
                                        class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 underline"
                                    >Cancel</button>
                                @endif
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">Submitted {{ $req->created_at->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</x-filament-panels::page>
