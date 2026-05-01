<?php

namespace App\Filament\Resources\EmployeeProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmergencyContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'emergencyContacts';
    protected static ?string $title = 'Emergency Contacts';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(200),
            Forms\Components\TextInput::make('relationship')->required()->maxLength(100),
            Forms\Components\TextInput::make('phone')->required()->tel()->maxLength(20),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('relationship'),
            Tables\Columns\TextColumn::make('phone'),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
