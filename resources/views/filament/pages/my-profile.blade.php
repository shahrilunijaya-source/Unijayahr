<x-filament-panels::page>
    @if(auth()->user()->must_change_password)
        <div class="mb-4 rounded-lg border border-warning-300 bg-warning-50 px-4 py-3 text-sm text-warning-700 dark:border-warning-700 dark:bg-warning-900/20 dark:text-warning-400">
            Action required: please change your password before continuing.
        </div>
    @endif

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" color="primary">
                Save changes
            </x-filament::button>
        </div>
    </form>

    {{-- Existing documents --}}
    @php $docs = auth()->user()->documents()->latest()->get(); @endphp
    @if($docs->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-3">My Documents</h2>
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Document</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Size</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Uploaded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @foreach($docs as $doc)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $doc->display_label }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                    @if($doc->file_size) {{ number_format($doc->file_size / 1024, 1) }} KB @else — @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
