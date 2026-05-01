<?php

namespace App\Filament\Resources\KpiRubricTemplateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Rubric Items';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('criterion')
                ->required()
                ->maxLength(200)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('description')
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('category')
                ->maxLength(100)
                ->placeholder('e.g. Performance, Behaviour'),
            Forms\Components\TextInput::make('weight')
                ->numeric()
                ->default(33)
                ->required()
                ->suffix('%')
                ->helperText('Weights should sum to 100 across all items'),
            Forms\Components\TextInput::make('max_score')
                ->numeric()
                ->default(5)
                ->required()
                ->minValue(1)
                ->maxValue(10),
            Forms\Components\TextInput::make('order')
                ->numeric()
                ->default(1)
                ->required(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('#')->width(40)->sortable(),
                Tables\Columns\TextColumn::make('criterion')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('weight')->suffix('%'),
                Tables\Columns\TextColumn::make('max_score')->label('Max'),
            ])
            ->defaultSort('order')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
