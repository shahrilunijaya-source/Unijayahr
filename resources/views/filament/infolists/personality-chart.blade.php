@php
    $result   = $getState();
    $scores   = $result['scores']   ?? [];
    $dominant = $result['dominant'] ?? null;
    $mbti     = $result['mbti']     ?? null;
    $goals    = $result['goals']    ?? null;
    $strengths  = $result['strengths']  ?? null;
    $weaknesses = $result['weaknesses'] ?? null;
    $interests  = $result['interests']  ?? null;

    $animalColors = [
        'Rabbit'   => '#f43f5e',
        'Tortoise' => '#22c55e',
        'Fox'      => '#f97316',
        'Sloth'    => '#a855f7',
    ];
    $animalEmoji = [
        'Rabbit'   => '🐇',
        'Tortoise' => '🐢',
        'Fox'      => '🦊',
        'Sloth'    => '🦥',
    ];
@endphp

@if(empty($scores))
    <p class="text-sm text-gray-400">No scores available.</p>
@else
    {{-- Animal result --}}
    @if($dominant)
        @php
            $color = $animalColors[$dominant] ?? '#d97706';
            $emoji = $animalEmoji[$dominant] ?? '';
        @endphp
        <div class="mb-4 rounded-lg px-4 py-3 border" style="border-color: {{ $color }}20; background-color: {{ $color }}10;">
            <p class="text-lg font-bold" style="color: {{ $color }}">{{ $emoji }} {{ $dominant }}</p>
            @if($mbti)
                <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-semibold text-white" style="background-color: {{ $color }}">{{ strtoupper($mbti) }}</span>
            @endif
        </div>
    @endif

    {{-- Score bars --}}
    <div class="space-y-3 py-2">
        @foreach($scores as $animal => $pct)
            @php
                $isTop = $animal === $dominant;
                $color = $animalColors[$animal] ?? '#cbd5e1';
                $barColor = $isTop ? $color : '#e2e8f0';
            @endphp
            <div class="flex items-center gap-4">
                <span class="w-20 text-sm font-semibold" style="color: {{ $isTop ? $color : '#6b7280' }}">
                    {{ $animalEmoji[$animal] ?? '' }} {{ $animal }}
                </span>
                <div class="flex-1 h-4 rounded-full bg-gray-100 dark:bg-gray-700">
                    <div class="h-4 rounded-full transition-all"
                         style="width: {{ min($pct, 100) }}%; background-color: {{ $barColor }}">
                    </div>
                </div>
                <span class="w-10 text-right text-sm {{ $isTop ? 'font-bold' : 'text-gray-400' }}"
                      style="{{ $isTop ? 'color:' . $color : '' }}">
                    {{ $pct }}%
                </span>
            </div>
        @endforeach
    </div>

    {{-- Text fields --}}
    @if($goals || $strengths || $weaknesses || $interests)
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach([
                ['label' => 'Goals', 'value' => $goals, 'icon' => '🎯'],
                ['label' => 'Strengths', 'value' => $strengths, 'icon' => '💪'],
                ['label' => 'Weaknesses', 'value' => $weaknesses, 'icon' => '⚡'],
                ['label' => 'Interests', 'value' => $interests, 'icon' => '✨'],
            ] as $field)
                @if($field['value'])
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-3">
                        <p class="text-xs font-semibold text-gray-500 mb-1">{{ $field['icon'] }} {{ $field['label'] }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $field['value'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endif
