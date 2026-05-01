<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KpiReviewResource\Pages;
use App\Models\KpiReview;
use App\Models\KpiPeriod;
use App\Services\KpiCalculator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KpiReviewResource extends Resource
{
    protected static ?string $model = KpiReview::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'KPI';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Reviews';

    public static function getEloquentQuery(): Builder
    {
        $user  = auth()->user();
        $query = parent::getEloquentQuery()->with(['user', 'reviewer', 'period', 'scores']);

        if ($user->hasAnyRole(['admin', 'hr'])) {
            return $query;
        }

        if ($user->hasRole('manager')) {
            return $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('reviewer_id', $user->id)
                  ->orWhereHas('user', fn ($u) => $u->where('superior_id', $user->id));
            });
        }

        // staff: own reviews + reviews where they are reviewer
        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('reviewer_id', $user->id);
        });
    }

    public static function form(Form $form): Form
    {
        $user   = auth()->user();
        $record = $form->getRecord();

        $isSelf     = $record?->user_id === $user->id;
        $isReviewer = $record?->reviewer_id === $user->id;
        $isAdmin    = $user->hasAnyRole(['admin', 'hr']);

        $selfLocked     = ! ($isSelf && in_array($record?->status, ['pending_self'])) && ! $isAdmin;
        $superiorLocked = ! ($isReviewer && in_array($record?->status, ['pending_superior'])) && ! $isAdmin;

        return $form->schema([
            Forms\Components\Section::make('Review Summary')->schema([
                Forms\Components\Placeholder::make('user_name')
                    ->label('Staff')
                    ->content(fn ($record) => $record?->user?->name ?? '—'),
                Forms\Components\Placeholder::make('reviewer_name')
                    ->label('Reviewer')
                    ->content(fn ($record) => $record?->reviewer?->name ?? '—'),
                Forms\Components\Placeholder::make('period_label')
                    ->label('Period')
                    ->content(fn ($record) => $record?->period?->label ?? '—'),
                Forms\Components\Placeholder::make('status_display')
                    ->label('Status')
                    ->content(fn ($record) => ucwords(str_replace('_', ' ', $record?->status ?? '')))
                    ->columnSpanFull(),
            ])->columns(2)->collapsible(),

            Forms\Components\Section::make('KPI Scores')->schema(
                static::buildScoreFields($record, $selfLocked, $superiorLocked)
            ),
        ]);
    }

    protected static function buildScoreFields(?KpiReview $review, bool $selfLocked, bool $superiorLocked): array
    {
        if (! $review) return [];

        $fields = [];
        foreach ($review->scores as $score) {
            $fields[] = Forms\Components\Fieldset::make($score->item_label_snapshot)
                ->schema([
                    Forms\Components\Select::make("scores.{$score->id}.self_score")
                        ->label('Self score (1–' . $score->max_score_snapshot . ')')
                        ->options(array_combine(range(1, $score->max_score_snapshot), range(1, $score->max_score_snapshot)))
                        ->disabled($selfLocked)
                        ->dehydrated(! $selfLocked),
                    Forms\Components\Textarea::make("scores.{$score->id}.self_comment")
                        ->label('Self comment')
                        ->rows(2)
                        ->disabled($selfLocked)
                        ->dehydrated(! $selfLocked),
                    Forms\Components\Select::make("scores.{$score->id}.superior_score")
                        ->label('Superior score (1–' . $score->max_score_snapshot . ')')
                        ->options(array_combine(range(1, $score->max_score_snapshot), range(1, $score->max_score_snapshot)))
                        ->disabled($superiorLocked)
                        ->dehydrated(! $superiorLocked),
                    Forms\Components\Textarea::make("scores.{$score->id}.superior_comment")
                        ->label('Superior comment')
                        ->rows(2)
                        ->disabled($superiorLocked)
                        ->dehydrated(! $superiorLocked),
                ])->columns(2);
        }

        return $fields;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Staff')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reviewer.name')->label('Reviewer'),
                Tables\Columns\TextColumn::make('period.label')->label('Period')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending_self', 'primary' => 'pending_superior', 'success' => 'finalized']),
                Tables\Columns\TextColumn::make('final_total')->label('Score')->numeric(2),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Period')
                    ->options(fn () => KpiPeriod::pluck('label', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending_self' => 'Pending Self', 'pending_superior' => 'Pending Superior', 'finalized' => 'Finalized']),
            ])
            ->actions([
                Tables\Actions\Action::make('submit_self')
                    ->label('Submit Self')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->visible(fn ($record) => $record->user_id === auth()->id() && $record->status === 'pending_self')
                    ->requiresConfirmation()
                    ->action(fn ($record) => app(KpiCalculator::class)->calculateSelf($record)),
                Tables\Actions\Action::make('submit_superior')
                    ->label('Submit Review')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->reviewer_id === auth()->id() && $record->status === 'pending_superior')
                    ->requiresConfirmation()
                    ->action(fn ($record) => app(KpiCalculator::class)->calculateSuperior($record)),
                Tables\Actions\Action::make('unlock')
                    ->label('Unlock')
                    ->icon('heroicon-o-lock-open')
                    ->color('warning')
                    ->visible(fn ($record) => auth()->user()->hasAnyRole(['admin', 'hr']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        if ($record->status === 'finalized') {
                            $record->update([
                                'status'                => 'pending_superior',
                                'pending_role'          => 'reviewer',
                                'superior_total'        => null,
                                'final_total'           => null,
                                'superior_submitted_at' => null,
                                'finalized_at'          => null,
                            ]);
                            $record->scores()->update(['superior_score' => null, 'superior_comment' => null]);
                        } elseif ($record->status === 'pending_superior') {
                            $record->update([
                                'status'           => 'pending_self',
                                'pending_role'     => 'user',
                                'self_total'       => null,
                                'self_submitted_at'=> null,
                            ]);
                            $record->scores()->update(['self_score' => null, 'self_comment' => null]);
                        }
                        Notification::make()->title('Review unlocked.')->warning()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpiReviews::route('/'),
            'edit'  => Pages\EditKpiReview::route('/{record}/edit'),
        ];
    }
}
