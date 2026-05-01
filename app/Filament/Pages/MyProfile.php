<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class MyProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $navigationGroup = 'Account';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.my-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->form->fill([
            'name'  => $user->name,
            'phone' => $user->phone,
        ]);
    }

    public function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->statePath('data')->schema([
            Forms\Components\Section::make('Your Details (read-only)')->schema([
                Forms\Components\Placeholder::make('email_display')
                    ->label('Email')
                    ->content($user->email),
                Forms\Components\Placeholder::make('job_title_display')
                    ->label('Job Title')
                    ->content($user->job_title ?? '—'),
                Forms\Components\Placeholder::make('unit_display')
                    ->label('Unit / Department')
                    ->content($user->unit ? "{$user->unit->department->name} → {$user->unit->name}" : '—'),
                Forms\Components\Placeholder::make('level_display')
                    ->label('Level')
                    ->content($user->level?->name ?? '—'),
                Forms\Components\Placeholder::make('join_date_display')
                    ->label('Join Date')
                    ->content($user->join_date?->format('d M Y') ?? '—'),
            ])->columns(2)->collapsible(),

            Forms\Components\Section::make('Edit Your Details')->schema([
                Forms\Components\TextInput::make('phone')
                    ->tel()->maxLength(20),
                Forms\Components\FileUpload::make('avatar_path')
                    ->label('Avatar')
                    ->image()
                    ->directory('avatars')
                    ->disk('public')
                    ->imageEditor()
                    ->nullable(),
            ]),

            Forms\Components\Section::make('Change Password')->schema([
                Forms\Components\TextInput::make('current_password')
                    ->password()
                    ->dehydrated(false)
                    ->nullable(),
                Forms\Components\TextInput::make('new_password')
                    ->password()
                    ->confirmed()
                    ->minLength(8)
                    ->nullable(),
                Forms\Components\TextInput::make('new_password_confirmation')
                    ->password()
                    ->label('Confirm new password')
                    ->dehydrated(false)
                    ->nullable(),
            ])->columns(2),
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $update = ['phone' => $data['phone'] ?? null];

        if (! empty($data['avatar_path'])) {
            $update['avatar_path'] = $data['avatar_path'];
        }

        if (! empty($data['new_password'])) {
            if (! Hash::check($data['current_password'] ?? '', $user->password)) {
                Notification::make()->title('Current password is incorrect.')->danger()->send();
                return;
            }
            $update['password']             = Hash::make($data['new_password']);
            $update['must_change_password'] = false;
        }

        $user->forceFill($update)->save();

        Notification::make()->title('Profile saved.')->success()->send();
    }
}
