<?php

namespace App\Filament\Resources\EmployeeProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EducationRelationManager extends RelationManager
{
    protected static string $relationship = 'education';
    protected static ?string $title = 'Education History';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('institution')->required()->maxLength(300)->columnSpanFull(),
            Forms\Components\Select::make('qualification')
                ->options([
                    'SPM'     => 'SPM',
                    'Diploma' => 'Diploma',
                    'Degree'  => 'Degree',
                    'Masters' => 'Masters',
                    'PhD'     => 'PhD',
                    'Other'   => 'Other',
                ])
                ->required(),
            Forms\Components\TextInput::make('field_of_study')->maxLength(200),
            Forms\Components\TextInput::make('year_completed')
                ->numeric()
                ->minValue(1970)
                ->maxValue(date('Y')),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('qualification')->badge(),
            Tables\Columns\TextColumn::make('institution'),
            Tables\Columns\TextColumn::make('field_of_study'),
            Tables\Columns\TextColumn::make('year_completed'),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
