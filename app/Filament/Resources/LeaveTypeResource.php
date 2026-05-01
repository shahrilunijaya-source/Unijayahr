<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LeaveTypeResource\Pages;
use App\Models\LeaveType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveTypeResource extends Resource
{
    protected static ?string $model           = LeaveType::class;
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Leave';
    protected static ?string $navigationLabel = 'Leave Types';
    protected static ?int    $navigationSort  = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(100),
            Forms\Components\TextInput::make('code')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50)
                ->helperText('Lowercase, no spaces. e.g. annual, mc'),
            Forms\Components\Toggle::make('is_paid')->label('Paid leave')->default(true),
            Forms\Components\Toggle::make('requires_document')->label('Requires document (e.g. MC cert)'),
            Forms\Components\TextInput::make('max_days_per_year')
                ->numeric()
                ->nullable()
                ->minValue(0)
                ->helperText('Leave blank for unlimited'),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable(),
            Tables\Columns\TextColumn::make('code')->badge(),
            Tables\Columns\IconColumn::make('is_paid')->boolean()->label('Paid'),
            Tables\Columns\IconColumn::make('requires_document')->boolean()->label('Doc Required'),
            Tables\Columns\TextColumn::make('max_days_per_year')
                ->label('Max Days/Yr')
                ->formatStateUsing(fn ($state) => $state !== null ? $state : '—'),
            Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'edit'   => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
