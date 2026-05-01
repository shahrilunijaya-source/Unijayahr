<x-filament-panels::page.simple>

    {{-- Brand block (above form) --}}
    <div class="mb-6">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg" style="background:#0d1b2e;">
                <svg class="h-5 w-5" style="color:#f5a623;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.584 2.376a.75.75 0 0 1 .832 0l9 6a.75.75 0 1 1-.832 1.248L12 3.901 3.416 9.624a.75.75 0 0 1-.832-1.248l9-6Z"/>
                    <path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1 0-1.5h.75v-9.918a.75.75 0 0 1 .634-.74A49.109 49.109 0 0 1 12 9c2.59 0 5.134.202 7.616.592a.75.75 0 0 1 .634.74Zm-7.5 2.418a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Zm3-.75a.75.75 0 0 1 .75.75v6.75a.75.75 0 0 1-1.5 0v-6.75a.75.75 0 0 1 .75-.75Zm-6 .75a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="text-lg font-bold" style="color:#0d1b2e;">Unijaya HR</span>
        </div>
        <div class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs" style="border-color:#e5e7eb;background:#f9fafb;color:#6b7280;">
            <span class="h-1.5 w-1.5 rounded-full" style="background:#16a34a;"></span>
            Unijaya Resources Sdn Bhd
        </div>
    </div>

    {{-- Form --}}
    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{-- Google OAuth --}}
    @if(config('services.google.client_id'))
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center">
                <span class="w-full border-t" style="border-color:#e5e7eb;"></span>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="px-2" style="background:#fff;color:#9ca3af;">atau</span>
            </div>
        </div>
        <a href="{{ route('auth.google') }}"
           class="flex w-full items-center justify-center gap-3 rounded-lg border px-4 py-2.5 text-sm font-medium transition hover:opacity-80"
           style="border-color:#e5e7eb;color:#374151;">
            <svg class="h-4 w-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Daftar masuk dengan Google
        </a>
    @endif

</x-filament-panels::page.simple>
