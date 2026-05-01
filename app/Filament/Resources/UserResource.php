<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Level;
use App\Models\Unit;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identity')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()->maxLength(255),
                Forms\Components\TextInput::make('job_title')
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()->maxLength(20),
                Forms\Components\TextInput::make('ic_number')
                    ->label('IC Number')
                    ->password()
                    ->maxLength(14)
                    ->helperText('Stored encrypted. Leave blank to keep existing.'),
            ])->columns(2),

            Forms\Components\Section::make('Organisation')->schema([
                Forms\Components\Select::make('unit_id')
                    ->label('Unit / Department')
                    ->options(fn () => Unit::with('department')->get()->mapWithKeys(
                        fn ($unit) => [$unit->id => "{$unit->department->name} → {$unit->name}"]
                    ))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('level_id')
                    ->label('Level')
                    ->options(fn () => Level::orderBy('order')->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('superior_id')
                    ->label('Superior')
                    ->options(fn () => User::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\DatePicker::make('join_date'),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'on_leave' => 'On Leave'])
                    ->default('active')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Can log in')
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Access')->schema([
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->options(Role::pluck('name', 'name'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Forms\Components\Toggle::make('must_change_password')
                    ->label('Force password change on next login'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=533afd&background=e5e3ff')
                    ->width(36)->height(36),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('job_title')->searchable(),
                Tables\Columns\TextColumn::make('unit.name')->label('Unit'),
                Tables\Columns\TextColumn::make('level.name')->label('Level')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'warning' => 'on_leave', 'danger' => 'inactive']),
                Tables\Columns\IconColumn::make('is_active')->label('Login')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->options(fn () => Unit::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('level_id')
                    ->label('Level')
                    ->options(fn () => Level::orderBy('order')->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('reset_password')
                    ->label('Reset passwords to temp')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $credentials = [];
                        foreach ($records as $user) {
                            $tempPass = Str::random(12);
                            $user->forceFill([
                                'password'             => Hash::make($tempPass),
                                'must_change_password' => true,
                            ])->save();
                            $credentials[] = "{$user->name} <{$user->email}>: {$tempPass}";
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Passwords reset')
                            ->body(implode("\n", $credentials))
                            ->warning()
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
            'view'   => Pages\ViewUser::route('/{record}'),
        ];
    }
}
