<x-filament-panels::page>
    {{-- TODAY STATUS CARD --}}
    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Today — {{ today()->format('l, d M Y') }}</h2>

        @php $rec = $this->todayRecord; @endphp

        @if($rec?->isComplete())
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $rec->is_late ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ $rec->statusLabel() }}
                </span>
                <span class="text-gray-600 dark:text-gray-300 text-sm">In: {{ $rec->clock_in_at?->format('H:i') }} · Out: {{ $rec->clock_out_at?->format('H:i') }} · {{ $rec->total_hours }}h</span>
            </div>
        @elseif($rec?->isClockedIn())
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">In Progress</span>
                <span class="text-gray-600 dark:text-gray-300 text-sm">In: {{ $rec->clock_in_at?->format('H:i') }}</span>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Not clocked in yet.</p>
        @endif

        {{-- Camera status --}}
        <div class="flex gap-2 mb-4" id="status-pills">
            <span id="camera-status" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                <span class="w-2 h-2 rounded-full bg-gray-400 inline-block" id="camera-dot"></span> Camera
            </span>
            <span id="gps-status" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                <span class="w-2 h-2 rounded-full bg-gray-400 inline-block" id="gps-dot"></span> GPS
            </span>
        </div>

        {{-- Clock button --}}
        @if($rec?->isComplete())
            <button disabled class="px-6 py-3 rounded-lg bg-gray-300 text-gray-500 font-semibold cursor-not-allowed">Done for today</button>
        @elseif($rec?->isClockedIn())
            <button id="clock-btn" class="px-6 py-3 rounded-lg bg-orange-600 hover:bg-orange-700 text-white font-semibold" data-action="out">Clock Out</button>
        @else
            <button id="clock-btn" class="px-6 py-3 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold" data-action="in">Clock In</button>
        @endif

        {{-- Hidden webcam elements --}}
        <video id="webcam" autoplay playsinline muted class="hidden w-48 h-36 rounded mt-4"></video>
        <canvas id="snapshot" class="hidden"></canvas>
    </div>

    {{-- HISTORY TABLE --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Last 30 Days</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Clock In</th>
                        <th class="px-6 py-3">Clock Out</th>
                        <th class="px-6 py-3">Hours</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Location</th>
                        <th class="px-6 py-3">Photos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->recentRecords as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $row->date->format('d M') }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $row->clock_in_at?->format('H:i') ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $row->clock_out_at?->format('H:i') ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $row->total_hours ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $row->is_late ? 'bg-red-100 text-red-700' : ($row->isComplete() ? 'bg-green-100 text-green-700' : ($row->isClockedIn() ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ $row->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                @if($row->clockInMapUrl())
                                    <a href="{{ $row->clockInMapUrl() }}" target="_blank" class="text-blue-600 hover:underline text-xs">In</a>
                                @endif
                                @if($row->clockOutMapUrl())
                                    <a href="{{ $row->clockOutMapUrl() }}" target="_blank" class="text-blue-600 hover:underline text-xs">Out</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                @if($row->clock_in_photo_path)
                                    <a href="{{ route('attendance.photo', [$row, 'in']) }}" target="_blank">
                                        <img src="{{ route('attendance.photo', [$row, 'in']) }}" class="w-10 h-10 rounded object-cover inline-block" alt="In">
                                    </a>
                                @endif
                                @if($row->clock_out_photo_path)
                                    <a href="{{ route('attendance.photo', [$row, 'out']) }}" target="_blank">
                                        <img src="{{ route('attendance.photo', [$row, 'out']) }}" class="w-10 h-10 rounded object-cover inline-block" alt="Out">
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">No records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- WEBCAM + GPS SCRIPT --}}
    <script>
    (function () {
        let stream = null;
        let gpsCoords = null;
        const video   = document.getElementById('webcam');
        const canvas  = document.getElementById('snapshot');
        const btn     = document.getElementById('clock-btn');
        const camDot  = document.getElementById('camera-dot');
        const gpsDot  = document.getElementById('gps-dot');

        if (!btn) return; // disabled state

        // Start camera
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
            .then(s => {
                stream = s;
                video.srcObject = s;
                camDot.classList.replace('bg-gray-400', 'bg-green-500');
            })
            .catch(() => {
                camDot.classList.replace('bg-gray-400', 'bg-red-500');
            });

        // Watch GPS
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                pos => {
                    gpsCoords = pos.coords;
                    gpsDot.classList.replace('bg-gray-400', 'bg-green-500');
                },
                () => {
                    gpsDot.classList.replace('bg-gray-400', 'bg-red-500');
                },
                { enableHighAccuracy: true }
            );
        }

        btn.addEventListener('click', function () {
            if (!gpsCoords) {
                alert('Waiting for GPS signal. Please try again in a moment.');
                return;
            }
            if (!stream) {
                alert('Camera not available. Please allow camera access and reload.');
                return;
            }

            // Snapshot
            canvas.width  = video.videoWidth  || 640;
            canvas.height = video.videoHeight || 480;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.7);

            // Stop camera tracks
            stream.getTracks().forEach(t => t.stop());

            const action = btn.dataset.action === 'out' ? 'clockOutAction' : 'clockInAction';
            @this.call(action, gpsCoords.latitude, gpsCoords.longitude, gpsCoords.accuracy, dataUrl);
        });
    })();
    </script>
</x-filament-panels::page>
