<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffDirectoryResource\Pages;
use App\Models\Department;
use App\Models\Level;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffDirectoryResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Staff Directory';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'staff-directory';

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['level', 'unit.department'])->orderBy('name');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=533afd&background=e5e3ff')
                    ->width(40)->height(40),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('job_title')->label('Title')->searchable(),
                Tables\Columns\TextColumn::make('unit.department.name')->label('Department'),
                Tables\Columns\TextColumn::make('unit.name')->label('Unit'),
                Tables\Columns\TextColumn::make('level.name')->label('Level')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'warning' => 'on_leave', 'danger' => 'inactive']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('Department')
                    ->options(fn () => Department::pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) =>
                        $data['value'] ? $query->whereHas('unit', fn ($q) => $q->where('department_id', $data['value'])) : $query
                    ),
                Tables\Filters\SelectFilter::make('level_id')
                    ->label('Level')
                    ->options(fn () => Level::orderBy('order')->pluck('name', 'id')),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffDirectory::route('/'),
            'view'  => Pages\ViewStaff::route('/{record}'),
        ];
    }
}
