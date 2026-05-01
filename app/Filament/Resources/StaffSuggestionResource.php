<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffSuggestionResource\Pages;
use App\Models\StaffSuggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffSuggestionResource extends Resource
{
    protected static ?string $model          = StaffSuggestion::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'People';
    protected static ?string $navigationLabel = 'Suggestions';
    protected static ?int    $navigationSort  = 5;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr', 'manager']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->visibleTo(auth()->user())
            ->with(['author', 'responder']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Submission')->schema([
                Infolists\Components\TextEntry::make('from_label')
                    ->label('From')
                    ->getStateUsing(fn ($record) => $record->is_anonymous ? '— Anonymous —' : ($record->author?->name ?? '—')),
                Infolists\Components\TextEntry::make('category')->badge(),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => $state === 'new' ? 'warning' : 'success'),
                Infolists\Components\TextEntry::make('created_at')->label('Submitted')->date(),
                Infolists\Components\TextEntry::make('title')->columnSpanFull(),
                Infolists\Components\TextEntry::make('body')->columnSpanFull()->prose(),
            ])->columns(2),

            Infolists\Components\Section::make('Management Response')
                ->hidden(fn ($record) => $record->status === 'new')
                ->schema([
                    Infolists\Components\TextEntry::make('management_response')->label('Response')->columnSpanFull()->prose(),
                    Infolists\Components\TextEntry::make('responder.name')->label('Responded by'),
                    Infolists\Components\TextEntry::make('responded_at')->label('Date')->date(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('from_label')
                    ->label('From')
                    ->getStateUsing(fn ($record) => $record->is_anonymous ? '— Anonymous —' : ($record->author?->name ?? '—'))
                    ->badge()
                    ->color(fn ($record) => $record->is_anonymous ? 'gray' : 'primary'),
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state === 'new' ? 'warning' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['new' => 'New', 'closed' => 'Closed']),
                Tables\Filters\SelectFilter::make('category')
                    ->options(array_combine(StaffSuggestion::CATEGORIES, StaffSuggestion::CATEGORIES)),
                Tables\Filters\TernaryFilter::make('is_anonymous')
                    ->label('Anonymous'),
            ])
            ->defaultSort('status')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('respond')
                    ->label('Respond')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'new' && auth()->user()->hasAnyRole(['admin', 'hr']))
                    ->form([
                        Forms\Components\Textarea::make('management_response')
                            ->label('Your response')
                            ->rows(4)
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'status'              => 'closed',
                            'management_response' => $data['management_response'],
                            'responded_at'        => now(),
                            'responder_id'        => auth()->id(),
                        ]);
                        Notification::make()->title('Response sent and suggestion closed.')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffSuggestions::route('/'),
            'view'  => Pages\ViewStaffSuggestion::route('/{record}'),
        ];
    }
}
