<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalityResultResource\Pages;
use App\Models\PersonalityResponse;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PersonalityResultResource extends Resource
{
    protected static ?string $model = PersonalityResponse::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Results';
    protected static ?string $modelLabel = 'Personality Result';
    protected static ?string $navigationGroup = 'Personality';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->query(PersonalityResponse::query()->whereNotNull('submitted_at')->with(['user', 'assessment']))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assessment.name')
                    ->label('Assessment')
                    ->sortable(),
                Tables\Columns\TextColumn::make('computed_result.dominant')
                    ->label('Animal')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Staff')->schema([
                Infolists\Components\TextEntry::make('user.name')->label('Name'),
                Infolists\Components\TextEntry::make('assessment.name')->label('Assessment'),
                Infolists\Components\TextEntry::make('submitted_at')->label('Submitted')->date(),
                Infolists\Components\TextEntry::make('computed_result.dominant')
                    ->label('Animal type')
                    ->badge()
                    ->color('warning'),
                Infolists\Components\TextEntry::make('computed_result.mbti')
                    ->label('MBTI')
                    ->badge()
                    ->color('gray'),
            ])->columns(4),

            Infolists\Components\Section::make('Axis Scores')->schema([
                Infolists\Components\ViewEntry::make('computed_result')
                    ->label('')
                    ->view('filament.infolists.personality-chart'),
            ]),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalityResults::route('/'),
            'view'  => Pages\ViewPersonalityResult::route('/{record}'),
        ];
    }
}
