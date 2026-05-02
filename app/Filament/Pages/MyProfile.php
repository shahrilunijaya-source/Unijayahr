<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $user    = auth()->user();
        $profile = $user->employeeProfile;

        $this->form->fill([
            'name'  => $user->name,
            'phone' => $user->phone,
            // personal info
            'date_of_birth'  => $profile?->date_of_birth?->format('Y-m-d'),
            'gender'         => $profile?->gender,
            'marital_status' => $profile?->marital_status,
            'address'        => $profile?->address,
            'city'           => $profile?->city,
            'state'          => $profile?->state,
            'postcode'       => $profile?->postcode,
            'nationality'    => $profile?->nationality ?? 'Malaysian',
            // documents repeater starts empty
            'new_documents'  => [],
        ]);
    }

    public function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->statePath('data')->schema([
            Forms\Components\Tabs::make('profile_tabs')->tabs([

                Forms\Components\Tabs\Tab::make('Account')->schema([
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
                ]),

                Forms\Components\Tabs\Tab::make('Personal Info')->schema([
                    Forms\Components\Section::make('Personal Information')->schema([
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->nullable()
                            ->maxDate(now()->subYears(16)),
                        Forms\Components\Select::make('gender')
                            ->options(['male' => 'Male', 'female' => 'Female'])
                            ->nullable(),
                        Forms\Components\Select::make('marital_status')
                            ->options(['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'])
                            ->nullable(),
                        Forms\Components\TextInput::make('nationality')
                            ->default('Malaysian')
                            ->maxLength(100),
                    ])->columns(2),

                    Forms\Components\Section::make('Address')->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\TextInput::make('city')->nullable()->maxLength(100),
                        Forms\Components\TextInput::make('state')->nullable()->maxLength(100),
                        Forms\Components\TextInput::make('postcode')->nullable()->maxLength(10),
                    ])->columns(2),
                ]),

                Forms\Components\Tabs\Tab::make('Documents')->schema([
                    Forms\Components\Section::make('Upload Documents')
                        ->description('Add your personal documents. Accepted: PDF, JPG, PNG, DOCX. Max 10 MB each.')
                        ->schema([
                            Forms\Components\Repeater::make('new_documents')
                                ->label(false)
                                ->addActionLabel('Add document')
                                ->schema([
                                    Forms\Components\Select::make('category')
                                        ->options(\App\Models\EmployeeDocument::CATEGORIES)
                                        ->required()
                                        ->live(),
                                    Forms\Components\TextInput::make('label')
                                        ->label('Document name')
                                        ->placeholder('Required when category is Other')
                                        ->visible(fn (\Filament\Forms\Get $get): bool => $get('category') === 'other')
                                        ->required(fn (\Filament\Forms\Get $get): bool => $get('category') === 'other')
                                        ->maxLength(200),
                                    Forms\Components\FileUpload::make('file')
                                        ->label('File')
                                        ->disk('local')
                                        ->directory('documents/' . auth()->id())
                                        ->visibility('private')
                                        ->required()
                                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                        ->maxSize(10240)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->collapsible(),
                        ]),
                ]),

            ])->columnSpanFull(),
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

        // Save personal profile
        $user->employeeProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'date_of_birth'  => $data['date_of_birth'] ?? null,
                'gender'         => $data['gender'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'address'        => $data['address'] ?? null,
                'city'           => $data['city'] ?? null,
                'state'          => $data['state'] ?? null,
                'postcode'       => $data['postcode'] ?? null,
                'nationality'    => $data['nationality'] ?? null,
            ]
        );

        // Save uploaded documents
        foreach ($data['new_documents'] ?? [] as $doc) {
            if (empty($doc['file'])) {
                continue;
            }
            $path     = $doc['file'];
            $fullPath = Storage::disk('local')->path($path);
            $size     = file_exists($fullPath) ? filesize($fullPath) : 0;
            $mime     = file_exists($fullPath) ? mime_content_type($fullPath) : null;

            \App\Models\EmployeeDocument::create([
                'user_id'     => $user->id,
                'category'    => $doc['category'],
                'label'       => ($doc['category'] === 'other') ? ($doc['label'] ?? null) : null,
                'file_path'   => $path,
                'file_size'   => $size,
                'mime_type'   => $mime,
                'uploaded_by' => $user->id,
            ]);
        }

        // Reset document repeater after save
        $this->data['new_documents'] = [];

        Notification::make()->title('Profile saved.')->success()->send();
    }
}
