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
</x-filament-panels::page>
