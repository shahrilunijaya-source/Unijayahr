<?php

namespace App\Filament\Pages;

use App\Models\StaffSuggestion;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MySuggestions extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-light-bulb';
    protected static ?string $navigationLabel = 'Suggestions';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.my-suggestions';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Select::make('category')
                ->label('Category')
                ->options(array_combine(StaffSuggestion::CATEGORIES, StaffSuggestion::CATEGORIES))
                ->required(),
            Forms\Components\TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(200),
            Forms\Components\Textarea::make('body')
                ->label('Your suggestion')
                ->rows(5)
                ->required()
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_anonymous')
                ->label('Submit anonymously')
                ->helperText('Only Shahril and Anis (HR) will see anonymous suggestions. Management cannot reply directly to anonymous submissions.')
                ->default(false),
        ])->columns(2);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit suggestion')
                ->submit('submit'),
        ];
    }

    public function submit(): void
    {
        $data      = $this->form->getState();
        $anonymous = (bool) ($data['is_anonymous'] ?? false);

        StaffSuggestion::create([
            'user_id'      => $anonymous ? null : auth()->id(),
            'is_anonymous' => $anonymous,
            'category'     => $data['category'],
            'title'        => $data['title'],
            'body'         => $data['body'],
            'status'       => 'new',
        ]);

        Notification::make()->title('Suggestion submitted. Thank you!')->success()->send();

        $this->form->fill();
    }

    public function getMySubmissions()
    {
        return StaffSuggestion::where('user_id', auth()->id())
            ->latest()
            ->get();
    }

    public function getPublicBoard()
    {
        return StaffSuggestion::query()
            ->visibleTo(auth()->user())
            ->where('is_anonymous', false)
            ->with('author')
            ->latest()
            ->limit(50)
            ->get();
    }
}
