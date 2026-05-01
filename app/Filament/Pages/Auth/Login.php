<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public function getTitle(): string | Htmlable
    {
        return 'Sign in to Unijaya HR';
    }

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    public function hasLogo(): bool
    {
        return false;
    }
}
