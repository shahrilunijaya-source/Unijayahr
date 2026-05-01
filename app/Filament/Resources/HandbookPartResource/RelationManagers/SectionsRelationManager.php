<?php

namespace App\Filament\Resources\HandbookPartResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';
    protected static ?string $title = 'Sections';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(200)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->required()->maxLength(200),
            Forms\Components\TextInput::make('order')->integer()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
            Forms\Components\MarkdownEditor::make('body')->columnSpanFull()->required(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('order')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
