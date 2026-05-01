<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LeaveBalanceResource\Pages;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveBalanceResource extends Resource
{
    protected static ?string $model           = LeaveBalance::class;
    protected static ?string $navigationIcon  = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Leave';
    protected static ?string $navigationLabel = 'Leave Balances';
    protected static ?int    $navigationSort  = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Staff')
                ->options(User::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('leave_type_id')
                ->label('Leave Type')
                ->options(LeaveType::where('is_active', true)->pluck('name', 'id'))
                ->required(),
            Forms\Components\TextInput::make('year')
                ->numeric()
                ->required()
                ->default(date('Y'))
                ->minValue(2020)
                ->maxValue(2030),
            Forms\Components\TextInput::make('allocated_days')
                ->numeric()
                ->required()
                ->minValue(0)
                ->step(0.5),
            Forms\Components\TextInput::make('carried_over')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->step(0.5),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('Staff')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('leaveType.name')->label('Leave Type'),
            Tables\Columns\TextColumn::make('year')->sortable(),
            Tables\Columns\TextColumn::make('allocated_days')->label('Allocated'),
            Tables\Columns\TextColumn::make('carried_over')->label('C/F'),
            Tables\Columns\TextColumn::make('used_days_display')
                ->label('Used')
                ->getStateUsing(fn ($record) => $record->usedDays()),
            Tables\Columns\TextColumn::make('remaining_days_display')
                ->label('Remaining')
                ->getStateUsing(fn ($record) => $record->remainingDays()),
        ])->filters([
            Tables\Filters\SelectFilter::make('year')
                ->options(array_combine(range(2024, 2028), range(2024, 2028)))
                ->default(date('Y')),
            Tables\Filters\SelectFilter::make('leave_type_id')
                ->label('Leave Type')
                ->relationship('leaveType', 'name'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->defaultSort('user.name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveBalances::route('/'),
            'create' => Pages\CreateLeaveBalance::route('/create'),
            'edit'   => Pages\EditLeaveBalance::route('/{record}/edit'),
        ];
    }
}
