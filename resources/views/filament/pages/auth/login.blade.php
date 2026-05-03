@vite(['resources/css/filament/app/theme.css'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal HR Unijaya - Sign In</title>
</head>
<body style="margin:0;padding:0;">
    <div class="hr-login-container" style="display:flex;min-height:100vh;background:#F8FAFC;">
        @include('filament.partials.login-panel-left')

        <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem;">
            <div style="width:100%;max-width:400px;">
                <h1 class="hr-login-heading" style="margin-bottom:0.5rem;font-size:1.5rem;color:#1F2937;">Portal HR Unijaya</h1>
                <p style="margin:0 0 1.5rem 0;color:#6B7280;font-size:0.875rem;">Masuk dengan akaun anda</p>

                <x-filament-panels::form id="form" wire:submit="authenticate">
                    {{ $this->form }}
                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </x-filament-panels::form>

                @if(config('services.google.client_id'))
                    <div style="position:relative;margin:1.5rem 0;">
                        <div style="position:absolute;inset:0;display:flex;align-items:center;">
                            <span style="width:100%;border-top:1px solid #e5e7eb;"></span>
                        </div>
                        <div style="position:relative;display:flex;justify-content:center;font-size:0.75rem;">
                            <span style="padding:0 0.5rem;background:#fff;color:#9ca3af;">atau</span>
                        </div>
                    </div>
                    <a href="{{ route('auth.google') }}"
                       style="display:flex;width:100%;align-items:center;justify-content:center;gap:0.75rem;border-radius:0.5rem;border:1px solid #e5e7eb;padding:0.625rem 1rem;font-size:0.875rem;font-weight:500;transition:opacity 0.2s;color:#374151;background:#fff;text-decoration:none;"
                       onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Daftar masuk dengan Google
                    </a>
                @endif

                <p style="text-align:center;margin-top:2rem;font-size:0.75rem;color:#9ca3af;">© 2026 Unijaya Resources Sdn Bhd</p>
            </div>
        </div>
    </div>
</body>
</html>
