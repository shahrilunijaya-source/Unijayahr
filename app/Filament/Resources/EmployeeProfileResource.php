<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeProfileResource\Pages;
use App\Filament\Resources\EmployeeProfileResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeProfileResource extends Resource
{
    protected static ?string $model           = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'People';
    protected static ?string $navigationLabel = 'Employee Profiles';
    protected static ?string $modelLabel      = 'Employee';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $slug            = 'employee-profiles';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Profile')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Personal')
                        ->schema([
                            Forms\Components\TextInput::make('name')->disabled(),
                            Forms\Components\TextInput::make('job_title')->disabled(),
                            Forms\Components\DatePicker::make('employeeProfile.date_of_birth')
                                ->label('Date of Birth'),
                            Forms\Components\Select::make('employeeProfile.gender')
                                ->label('Gender')
                                ->options(['male' => 'Male', 'female' => 'Female']),
                            Forms\Components\Select::make('employeeProfile.marital_status')
                                ->label('Marital Status')
                                ->options([
                                    'single'   => 'Single',
                                    'married'  => 'Married',
                                    'divorced' => 'Divorced',
                                    'widowed'  => 'Widowed',
                                ]),
                            Forms\Components\TextInput::make('employeeProfile.nationality')
                                ->label('Nationality')
                                ->default('Malaysian'),
                            Forms\Components\Textarea::make('employeeProfile.address')
                                ->label('Address')
                                ->columnSpanFull()
                                ->rows(2),
                            Forms\Components\TextInput::make('employeeProfile.city')->label('City'),
                            Forms\Components\TextInput::make('employeeProfile.state')->label('State'),
                            Forms\Components\TextInput::make('employeeProfile.postcode')->label('Postcode'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Bank')
                        ->schema([
                            Forms\Components\TextInput::make('bankDetail.bank_name')
                                ->label('Bank Name'),
                            Forms\Components\TextInput::make('bankDetail.account_holder_name')
                                ->label('Account Holder Name'),
                            Forms\Components\TextInput::make('bankDetail.account_number')
                                ->label('Account Number')
                                ->password()
                                ->revealable(),
                        ])->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('job_title')->searchable(),
                Tables\Columns\TextColumn::make('unit.name')->label('Unit'),
                Tables\Columns\TextColumn::make('employeeProfile.date_of_birth')
                    ->label('DOB')->date()->toggleable(),
                Tables\Columns\TextColumn::make('join_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'on_leave' => 'warning',
                        'inactive' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'   => 'Active',
                        'inactive' => 'Inactive',
                        'on_leave' => 'On Leave',
                    ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\EmergencyContactsRelationManager::class,
            RelationManagers\EducationRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeProfiles::route('/'),
            'edit'  => Pages\EditEmployeeProfile::route('/{record}/edit'),
        ];
    }
}
