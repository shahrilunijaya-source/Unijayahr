<x-filament-panels::page>
    {{-- Submission form --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Submit a Suggestion</h2>

        <form wire:submit="submit">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" color="primary">
                    Submit suggestion
                </x-filament::button>
            </div>
        </form>
    </div>

    {{-- My submissions --}}
    @php $mine = $this->getMySubmissions(); @endphp
    @if($mine->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">My Submissions</h2>
            <div class="space-y-3">
                @foreach($mine as $s)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $s->title }}</span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $s->category }}</span>
                                    @if($s->is_anonymous)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Anonymous</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $s->body }}</p>
                                @if($s->status === 'closed' && $s->management_response)
                                    <div class="mt-2 rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3">
                                        <p class="text-xs font-semibold text-green-700 dark:text-green-400 mb-0.5">Management response · {{ $s->responded_at?->format('d M Y') }}</p>
                                        <p class="text-sm text-green-800 dark:text-green-300 whitespace-pre-line">{{ $s->management_response }}</p>
                                    </div>
                                @endif
                            </div>
                            <span @class([
                                'inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400' => $s->status === 'new',
                                'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' => $s->status === 'closed',
                            ])>{{ ucfirst($s->status) }}</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">{{ $s->created_at->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Public board --}}
    @php $board = $this->getPublicBoard(); @endphp
    @if($board->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Staff Suggestion Board</h2>
            <p class="text-xs text-gray-400 mb-3">Suggestions submitted by name (anonymous submissions are not shown here).</p>
            <div class="space-y-3">
                @foreach($board as $s)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{ $s->author?->name ?? '—' }}</span>
                                    <span class="text-gray-300 dark:text-gray-600">·</span>
                                    <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $s->title }}</span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $s->category }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $s->body }}</p>
                                @if($s->status === 'closed' && $s->management_response)
                                    <div class="mt-2 rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3">
                                        <p class="text-xs font-semibold text-green-700 dark:text-green-400 mb-0.5">Management response · {{ $s->responded_at?->format('d M Y') }}</p>
                                        <p class="text-sm text-green-800 dark:text-green-300 whitespace-pre-line">{{ $s->management_response }}</p>
                                    </div>
                                @endif
                            </div>
                            <span @class([
                                'inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400' => $s->status === 'new',
                                'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' => $s->status === 'closed',
                            ])>{{ ucfirst($s->status) }}</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">{{ $s->created_at->format('d M Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-panels::page>
