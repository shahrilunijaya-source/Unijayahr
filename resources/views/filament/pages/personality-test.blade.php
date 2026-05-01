<x-filament-panels::page>
    @php
        $assessment = $this->getAssessment();
        $sections = $assessment?->sections ?? collect();
        $totalSteps = $sections->count();
        $currentSection = $sections->get($currentStep);

        $animalColors = ['Rabbit' => '#f43f5e', 'Tortoise' => '#22c55e', 'Fox' => '#f97316', 'Sloth' => '#a855f7'];
        $animalEmoji  = ['Rabbit' => '🐇', 'Tortoise' => '🐢', 'Fox' => '🦊', 'Sloth' => '🦥'];
    @endphp

    {{-- Already completed --}}
    @if($existingResponse && !$submitted)
        @php
            $scores   = $existingResponse->computed_result['scores']   ?? [];
            $dominant = $existingResponse->computed_result['dominant'] ?? null;
            $mbti     = $existingResponse->computed_result['mbti']     ?? null;
            $domColor = $dominant ? ($animalColors[$dominant] ?? '#d97706') : '#d97706';
        @endphp
        <div class="rounded-xl border p-6" style="border-color: {{ $domColor }}30; background-color: {{ $domColor }}08;">
            <h3 class="text-lg font-semibold mb-1" style="color: {{ $domColor }}">
                {{ $dominant ? (($animalEmoji[$dominant] ?? '') . ' You\'re a ' . $dominant . '!') : 'Already completed' }}
            </h3>
            @if($mbti)
                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold text-white mb-3" style="background-color: {{ $domColor }}">{{ strtoupper($mbti) }}</span>
            @endif
            <p class="text-sm text-gray-500 mb-4">Submitted {{ $existingResponse->submitted_at?->format('d M Y') }}.</p>
            <div class="space-y-2">
                @foreach($scores as $animal => $pct)
                    @php $isTop = $animal === $dominant; $c = $animalColors[$animal] ?? '#cbd5e1'; @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-20 text-sm font-semibold" style="color: {{ $isTop ? $c : '#9ca3af' }}">{{ $animalEmoji[$animal] ?? '' }} {{ $animal }}</span>
                        <div class="flex-1 h-3 rounded-full bg-gray-100 dark:bg-gray-700">
                            <div class="h-3 rounded-full" style="width: {{ min($pct, 100) }}%; background-color: {{ $isTop ? $c : '#e2e8f0' }}"></div>
                        </div>
                        <span class="text-sm w-10 text-right" style="color: {{ $isTop ? $c : '#9ca3af' }}">{{ $pct }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

    {{-- No assessment --}}
    @elseif(!$assessment || $totalSteps === 0)
        <div class="rounded-xl border border-gray-200 bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-800">
            <x-heroicon-o-sparkles class="mx-auto h-10 w-10 text-gray-300 mb-3" />
            <p class="text-gray-500">The personality assessment hasn't been set up yet. Check back later.</p>
        </div>

    {{-- Submitted: show result --}}
    @elseif($submitted && $existingResponse)
        @php
            $scores   = $existingResponse->computed_result['scores']   ?? [];
            $dominant = $existingResponse->computed_result['dominant'] ?? null;
            $mbti     = $existingResponse->computed_result['mbti']     ?? null;
            $goals    = $existingResponse->computed_result['goals']     ?? null;
            $strengths  = $existingResponse->computed_result['strengths']  ?? null;
            $weaknesses = $existingResponse->computed_result['weaknesses'] ?? null;
            $interests  = $existingResponse->computed_result['interests']  ?? null;
            $domColor = $dominant ? ($animalColors[$dominant] ?? '#f97316') : '#f97316';
        @endphp
        <div class="rounded-xl border p-6" style="border-color: {{ $domColor }}40; background-color: {{ $domColor }}08;">
            <h3 class="text-2xl font-bold mb-1" style="color: {{ $domColor }}">
                {{ $dominant ? (($animalEmoji[$dominant] ?? '') . ' You\'re a ' . $dominant . '!') : 'Your Results' }}
            </h3>
            @if($mbti)
                <span class="inline-block rounded-full px-3 py-1 text-sm font-bold text-white mb-4" style="background-color: {{ $domColor }}">MBTI: {{ strtoupper($mbti) }}</span>
            @endif

            <div class="space-y-2 mb-6">
                @foreach($scores as $animal => $pct)
                    @php $isTop = $animal === $dominant; $c = $animalColors[$animal] ?? '#cbd5e1'; @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-24 text-sm font-semibold" style="color: {{ $isTop ? $c : '#9ca3af' }}">{{ $animalEmoji[$animal] ?? '' }} {{ $animal }}</span>
                        <div class="flex-1 h-4 rounded-full bg-gray-100 dark:bg-gray-700">
                            <div class="h-4 rounded-full transition-all" style="width: {{ min($pct, 100) }}%; background-color: {{ $isTop ? $c : '#e2e8f0' }}"></div>
                        </div>
                        <span class="text-sm font-semibold w-10 text-right" style="color: {{ $isTop ? $c : '#9ca3af' }}">{{ $pct }}%</span>
                    </div>
                @endforeach
            </div>

            @if($goals || $strengths || $weaknesses || $interests)
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach([['🎯 Goals', $goals], ['💪 Strengths', $strengths], ['⚡ Weaknesses', $weaknesses], ['✨ Interests', $interests]] as [$label, $val])
                        @if($val)
                            <div class="rounded-lg bg-white/60 dark:bg-gray-800/60 p-3 border border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-500 mb-1">{{ $label }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $val }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

    {{-- Active test --}}
    @else
        {{-- Progress --}}
        <div class="mb-6">
            <div class="flex justify-between text-xs text-gray-400 mb-1">
                <span>Step {{ $currentStep + 1 }} of {{ $totalSteps }}</span>
                <span>{{ $currentSection?->title }}</span>
            </div>
            <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-700">
                <div class="h-1.5 rounded-full bg-amber-500 transition-all"
                     style="width: {{ $totalSteps > 0 ? (($currentStep + 1) / $totalSteps) * 100 : 0 }}%"></div>
            </div>
        </div>

        {{-- Questions --}}
        @if($currentSection)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                @if($currentSection->description)
                    <p class="mb-4 text-sm text-gray-500">{{ $currentSection->description }}</p>
                @endif

                <div class="space-y-6">
                    @foreach($currentSection->questions as $question)
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white mb-2">{{ $question->prompt }}</p>

                            @if($question->type === 'single')
                                <div class="space-y-2">
                                    @foreach($question->options ?? [] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer rounded-lg border border-gray-200 px-4 py-3 transition hover:border-amber-400 dark:border-gray-600 {{ ($answers[$question->id] ?? null) === $opt['value'] ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : '' }}">
                                            <input type="radio"
                                                   wire:model.live="answers.{{ $question->id }}"
                                                   value="{{ $opt['value'] }}"
                                                   class="text-amber-600 focus:ring-amber-500" />
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $opt['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($question->type === 'scale')
                                <div class="flex gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-1 flex-col items-center cursor-pointer">
                                            <input type="radio"
                                                   wire:model.live="answers.{{ $question->id }}"
                                                   value="{{ $i }}"
                                                   class="text-amber-600" />
                                            <span class="text-xs mt-1 text-gray-400">{{ $i }}</span>
                                        </label>
                                    @endfor
                                </div>
                            @elseif($question->type === 'text')
                                <textarea wire:model.live="answers.{{ $question->id }}"
                                          rows="3"
                                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                                          placeholder="Type here..."></textarea>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Navigation --}}
        <div class="mt-6 flex justify-between">
            @if($currentStep > 0)
                <x-filament::button color="gray" wire:click="prevStep">Previous</x-filament::button>
            @else
                <div></div>
            @endif

            @if($currentStep < $totalSteps - 1)
                <x-filament::button color="primary" wire:click="nextStep">Next</x-filament::button>
            @else
                <x-filament::button color="success" wire:click="submit">Submit</x-filament::button>
            @endif
        </div>
    @endif
</x-filament-panels::page>
