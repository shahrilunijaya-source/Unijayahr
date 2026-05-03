<x-filament-panels::page>
    <div class="flex items-center gap-4 mb-6">
        <label for="date-picker" class="font-medium text-gray-700 dark:text-gray-300">Date</label>
        <input
            id="date-picker"
            type="date"
            wire:model.live="selectedDate"
            class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500"
        >
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Staff</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Clock In</th>
                        <th class="px-6 py-3">Clock Out</th>
                        <th class="px-6 py-3">Hours</th>
                        <th class="px-6 py-3">Location</th>
                        <th class="px-6 py-3">Photos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->rows as $member)
                        @php $rec = $member->attendanceRecords->first(); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $member->name }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $rec ? ($rec->is_late ? 'bg-red-100 text-red-700' : ($rec->isComplete() ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700')) : 'bg-gray-100 text-gray-500' }}">
                                    {{ $rec ? $rec->statusLabel() : 'Absent' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $rec?->clock_in_at?->format('H:i') ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $rec?->clock_out_at?->format('H:i') ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $rec?->total_hours ?? '—' }}</td>
                            <td class="px-6 py-4 space-x-2">
                                @if($rec?->clockInMapUrl())
                                    <a href="{{ $rec->clockInMapUrl() }}" target="_blank" class="text-blue-600 hover:underline text-xs">In</a>
                                @endif
                                @if($rec?->clockOutMapUrl())
                                    <a href="{{ $rec->clockOutMapUrl() }}" target="_blank" class="text-blue-600 hover:underline text-xs">Out</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                @if($rec?->clock_in_photo_path)
                                    <a href="{{ route('attendance.photo', [$rec, 'in']) }}" target="_blank">
                                        <img src="{{ route('attendance.photo', [$rec, 'in']) }}" class="w-10 h-10 rounded object-cover inline-block" alt="In">
                                    </a>
                                @endif
                                @if($rec?->clock_out_photo_path)
                                    <a href="{{ route('attendance.photo', [$rec, 'out']) }}" target="_blank">
                                        <img src="{{ route('attendance.photo', [$rec, 'out']) }}" class="w-10 h-10 rounded object-cover inline-block" alt="Out">
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">No staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
