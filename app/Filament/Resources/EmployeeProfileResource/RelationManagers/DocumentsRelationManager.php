<?php
namespace App\Filament\Resources\EmployeeProfileResource\RelationManagers;

use App\Models\EmployeeDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('category')
                ->options(EmployeeDocument::CATEGORIES)
                ->required()
                ->live(),
            Forms\Components\TextInput::make('label')
                ->label('Document name')
                ->visible(fn (Forms\Get $get) => $get('category') === 'other')
                ->required(fn (Forms\Get $get) => $get('category') === 'other')
                ->maxLength(200),
            Forms\Components\FileUpload::make('file_path')
                ->label('File')
                ->disk('local')
                ->directory(fn ($record) => 'documents/' . ($record?->id ?? $this->getOwnerRecord()->id))
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->maxSize(10240)
                ->required()
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('display_label')->label('Document'),
            Tables\Columns\TextColumn::make('category')->badge(),
            Tables\Columns\TextColumn::make('uploader.name')->label('Uploaded by'),
            Tables\Columns\TextColumn::make('created_at')->label('Date')->date(),
        ])->headerActions([
            Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['uploaded_by'] = auth()->id();
                    return $data;
                }),
        ])->actions([
            Tables\Actions\Action::make('download')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('documents.download', ['id' => $record->id]))
                ->openUrlInNewTab(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
