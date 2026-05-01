<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KpiPeriodResource\Pages;
use App\Models\KpiPeriod;
use App\Models\KpiReview;
use App\Models\KpiReviewScore;
use App\Models\KpiRubricTemplate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KpiPeriodResource extends Resource
{
    protected static ?string $model = KpiPeriod::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'KPI';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Periods';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'hr']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')->required()->maxLength(100),
            Forms\Components\DatePicker::make('start_date')->required(),
            Forms\Components\DatePicker::make('end_date')->required(),
            Forms\Components\Select::make('status')
                ->options(['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed'])
                ->default('draft')->required(),
            Forms\Components\TextInput::make('self_weight')->numeric()->default(0.30)->required(),
            Forms\Components\TextInput::make('superior_weight')->numeric()->default(0.70)->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('start_date')->date('M Y'),
                Tables\Columns\TextColumn::make('end_date')->date('M Y'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['secondary' => 'draft', 'success' => 'open', 'danger' => 'closed']),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Reviews')
                    ->counts('reviews'),
            ])
            ->actions([
                Tables\Actions\Action::make('open_period')
                    ->label('Open')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(fn ($record) => self::openPeriod($record)),
                Tables\Actions\Action::make('close_period')
                    ->label('Close')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'closed', 'closed_at' => now()])),
                Tables\Actions\EditAction::make(),
            ]);
    }

    protected static function openPeriod(KpiPeriod $period): void
    {
        $period->update(['status' => 'open', 'opened_at' => now()]);

        $activeStaff = User::where('is_active', true)->whereNotNull('level_id')->get();
        $created = 0;

        foreach ($activeStaff as $staff) {
            $template = KpiRubricTemplate::where('level_id', $staff->level_id)
                ->where('is_active', true)
                ->latest()
                ->first();

            if (! $template) continue;

            $reviewer = $staff->superior ?? $staff;

            $review = KpiReview::firstOrCreate([
                'user_id'   => $staff->id,
                'period_id' => $period->id,
            ], [
                'reviewer_id'  => $reviewer->id,
                'template_id'  => $template->id,
                'status'       => 'pending_self',
                'pending_role' => 'user',
            ]);

            if ($review->wasRecentlyCreated) {
                // Snapshot rubric items
                foreach ($template->items as $item) {
                    KpiReviewScore::create([
                        'review_id'             => $review->id,
                        'item_id'               => $item->id,
                        'item_label_snapshot'   => $item->criterion,
                        'item_weight_snapshot'  => $item->weight,
                        'max_score_snapshot'    => $item->max_score,
                    ]);
                }
                $created++;
            }
        }

        Notification::make()
            ->title("Period opened. {$created} reviews created.")
            ->success()
            ->send();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKpiPeriods::route('/'),
            'create' => Pages\CreateKpiPeriod::route('/create'),
            'edit'   => Pages\EditKpiPeriod::route('/{record}/edit'),
        ];
    }
}
