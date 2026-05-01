<?php

namespace App\Filament\Resources\PersonalityAssessmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';
    protected static ?string $title = 'Sections & Questions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
            Forms\Components\TextInput::make('order')->numeric()->default(0)->required(),

            Forms\Components\Repeater::make('questions')
                ->relationship('questions')
                ->label('Questions')
                ->columnSpanFull()
                ->addActionLabel('Add question')
                ->reorderable(reorderColumn: 'order')
                ->schema([
                    Forms\Components\Textarea::make('prompt')
                        ->label('Question prompt')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('type')
                        ->options([
                            'single' => 'Single choice (radio)',
                            'scale'  => 'Scale 1–5',
                            'text'   => 'Free text',
                        ])
                        ->default('single')
                        ->required()
                        ->live(),

                    Forms\Components\TextInput::make('order')
                        ->numeric()
                        ->default(0)
                        ->hidden(),

                    Forms\Components\Textarea::make('options')
                        ->label('Options (JSON) — e.g. [{"value":"a","label":"Strongly Agree"}]')
                        ->rows(4)
                        ->columnSpanFull()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'single')
                        ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                        ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state),

                    Forms\Components\Textarea::make('scoring_weights')
                        ->label('Scoring weights (JSON) — e.g. {"a":{"D":2,"I":1}}')
                        ->rows(4)
                        ->columnSpanFull()
                        ->visible(fn (Forms\Get $get) => in_array($get('type'), ['single', 'scale']))
                        ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                        ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state),
                ])
                ->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions'),
                Tables\Columns\TextColumn::make('order')->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
