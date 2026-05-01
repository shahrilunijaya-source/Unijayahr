<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HandbookPartResource\Pages;
use App\Filament\Resources\HandbookPartResource\RelationManagers;
use App\Models\HandbookPart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HandbookPartResource extends Resource
{
    protected static ?string $model = HandbookPart::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Handbook Admin';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('code')->maxLength(2)->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('title')->required()->maxLength(100),
                Forms\Components\TextInput::make('tagline')->maxLength(200),
                Forms\Components\TextInput::make('icon')->maxLength(80)->helperText('e.g. heroicon-o-book-open'),
                Forms\Components\ColorPicker::make('accent_color'),
                Forms\Components\TextInput::make('order')->integer()->required()->default(0),
                Forms\Components\Toggle::make('is_published')->default(true),
                Forms\Components\RichEditor::make('intro_body')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('order')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('order')
            ->reorderable('order');
    }

    public static function getRelationManagers(): array
    {
        return [RelationManagers\SectionsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHandbookParts::route('/'),
            'create' => Pages\CreateHandbookPart::route('/create'),
            'edit'   => Pages\EditHandbookPart::route('/{record}/edit'),
        ];
    }
}
