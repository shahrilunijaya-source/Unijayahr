<x-filament-panels::page>

    @php $requests = $this->pendingRequests; @endphp

    @if($requests->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">No pending leave requests.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($requests as $req)
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $req->applicant->name }}</span>
                                <span class="text-gray-300 dark:text-gray-600">·</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300 text-sm">{{ $req->leaveType->name }}</span>
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                                    Pending
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $req->start_date->format('d M Y') }} – {{ $req->end_date->format('d M Y') }}
                                <span class="text-gray-400">({{ $req->total_days }} day(s))</span>
                            </p>
                            @if($req->reason)
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $req->reason }}</p>
                            @endif
                            <p class="mt-1 text-xs text-gray-400">Submitted {{ $req->created_at->format('d M Y, g:ia') }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                wire:click="approve({{ $req->id }})"
                                wire:confirm="Approve this leave request?"
                                class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600"
                            >Approve</button>
                            <button
                                wire:click="openRejectModal({{ $req->id }})"
                                class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600"
                            >Reject</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Reject modal --}}
    <div
        x-data="{ open: false }"
        x-on:open-reject-modal.window="open = true"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Reject Leave Request</h3>
            <textarea
                wire:model="rejectNote"
                rows="3"
                placeholder="Reason for rejection (optional)"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            ></textarea>
            <div class="mt-4 flex justify-end gap-3">
                <button
                    wire:click="cancelReject"
                    x-on:click="open = false"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                >Cancel</button>
                <button
                    wire:click="confirmReject"
                    x-on:click="open = false"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                >Confirm Reject</button>
            </div>
        </div>
    </div>

</x-filament-panels::page>
