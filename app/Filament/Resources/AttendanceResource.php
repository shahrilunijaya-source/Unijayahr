<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\AttendanceRecord;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;
    protected static ?string $navigationGroup = 'Attendance';
    protected static ?int $navigationSort = 21;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Records';
    protected static ?string $modelLabel = 'Attendance Record';
    protected static ?string $pluralModelLabel = 'Attendance Records';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->disabled(),

            Forms\Components\DatePicker::make('date')
                ->required()
                ->disabled(),

            Forms\Components\DateTimePicker::make('clock_in_at')
                ->label('Clock In'),

            Forms\Components\DateTimePicker::make('clock_out_at')
                ->label('Clock Out'),

            Forms\Components\Toggle::make('is_late')
                ->label('Is Late'),

            Forms\Components\TextInput::make('late_minutes')
                ->label('Late Minutes')
                ->numeric()
                ->minValue(0),

            Forms\Components\TextInput::make('total_hours')
                ->label('Total Hours')
                ->numeric()
                ->step(0.01),

            Forms\Components\Textarea::make('note')
                ->label('Admin Note')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('clock_in_at')
                    ->label('In')
                    ->dateTime('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('clock_out_at')
                    ->label('Out')
                    ->dateTime('H:i'),

                Tables\Columns\IconColumn::make('is_late')
                    ->label('Late')
                    ->boolean(),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Hours')
                    ->suffix('h'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $v) => $q->whereDate('date', '>=', $v))
                            ->when($data['until'], fn ($q, $v) => $q->whereDate('date', '<=', $v));
                    }),

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Staff'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'complete'   => 'Complete',
                        'incomplete' => 'Incomplete',
                        'late'       => 'Late',
                        'absent'     => 'Absent',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'complete'   => $query->whereNotNull('clock_in_at')->whereNotNull('clock_out_at'),
                            'incomplete' => $query->whereNotNull('clock_in_at')->whereNull('clock_out_at'),
                            'late'       => $query->where('is_late', true),
                            'absent'     => $query->whereNull('clock_in_at'),
                            default      => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAttendances::route('/'),
            'view'   => Pages\ViewAttendance::route('/{record}'),
            'edit'   => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
