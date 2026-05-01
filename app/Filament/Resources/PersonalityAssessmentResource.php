<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalityAssessmentResource\Pages;
use App\Filament\Resources\PersonalityAssessmentResource\RelationManagers;
use App\Models\PersonalityAssessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PersonalityAssessmentResource extends Resource
{
    protected static ?string $model = PersonalityAssessment::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Assessments';
    protected static ?string $navigationGroup = 'Personality';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Assessment')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active (visible to staff)')
                    ->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Scoring Configuration')
                ->description('JSON config — e.g. {"axes":["D","I","S","C"],"max_per_axis":40}')
                ->schema([
                    Forms\Components\Textarea::make('scoring_config')
                        ->label('scoring_config (JSON)')
                        ->rows(4)
                        ->columnSpanFull()
                        ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                        ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('sections_count')
                ->counts('sections')
                ->label('Sections')
                ->sortable(),
            Tables\Columns\IconColumn::make('is_active')
                ->label('Active')
                ->boolean(),
            Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPersonalityAssessments::route('/'),
            'create' => Pages\CreatePersonalityAssessment::route('/create'),
            'edit'   => Pages\EditPersonalityAssessment::route('/{record}/edit'),
        ];
    }
}
