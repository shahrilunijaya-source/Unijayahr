<x-filament-panels::page>
    {{-- Breadcrumb --}}
    @if($selectedPart || $selectedSection)
        <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
            <button wire:click="back" class="flex items-center gap-1 hover:text-stripe-purple transition">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
                Back
            </button>
            @if($selectedPart)
                <span>/</span>
                <span class="text-gray-700 dark:text-gray-300">Part {{ $selectedPart }}</span>
            @endif
            @if($selectedSection)
                <span>/</span>
                <span class="text-gray-700 dark:text-gray-300">{{ $this->getSelectedSectionModel()?->title }}</span>
            @endif
        </div>
    @endif

    {{-- Section detail view --}}
    @if($selectedSection)
        @php $section = $this->getSelectedSectionModel() @endphp
        <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-6 text-2xl font-semibold text-gray-900 dark:text-white">{{ $section?->title }}</h2>
            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! $this->getBodyHtml() !!}
            </div>
        </div>

    {{-- Part detail view --}}
    @elseif($selectedPart)
        @php $part = $this->getSelectedPartModel() @endphp
        <div class="mb-6 rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
             style="border-left: 4px solid {{ $part?->accent_color }}">
            <div class="flex items-center gap-3 mb-2">
                <x-dynamic-component :component="$part->icon" class="h-6 w-6" style="color: {{ $part?->accent_color }}" />
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Part {{ $part?->code }}</span>
            </div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $part?->title }}</h2>
            @if($part?->tagline)
                <p class="mt-1 text-gray-500">{{ $part?->tagline }}</p>
            @endif
            @if($part?->intro_body)
                <div class="mt-4 prose prose-sm max-w-none dark:prose-invert">{!! $part->intro_body !!}</div>
            @endif
        </div>

        @if($part?->sections->count())
            <div class="grid gap-3">
                @foreach($part->sections as $section)
                    <button wire:click="selectSection({{ $section->id }})"
                            class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-5 py-4 text-left shadow-sm transition hover:border-stripe-purple hover:shadow dark:border-gray-700 dark:bg-gray-800">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $section->title }}</span>
                        <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-400" />
                    </button>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400">No sections published for this part yet.</p>
        @endif

    {{-- Index: all parts as cards --}}
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->getParts() as $part)
                <button wire:click="selectPart('{{ $part->code }}')"
                        class="group flex flex-col rounded-xl border border-gray-200 bg-white p-5 text-left shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                        style="border-left: 4px solid {{ $part->accent_color }}">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="rounded-lg p-2" style="background-color: {{ $part->accent_color }}20">
                            <x-dynamic-component :component="$part->icon" class="h-5 w-5" style="color: {{ $part->accent_color }}" />
                        </div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Part {{ $part->code }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-stripe-purple dark:text-white">
                        {{ $part->title }}
                    </h3>
                    @if($part->tagline)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $part->tagline }}</p>
                    @endif
                    @if($part->sections->count())
                        <p class="mt-3 text-xs text-gray-400">{{ $part->sections->count() }} {{ Str::plural('section', $part->sections->count()) }}</p>
                    @endif
                </button>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
