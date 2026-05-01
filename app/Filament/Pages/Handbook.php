<?php

namespace App\Filament\Pages;

use App\Models\ContentSection;
use App\Models\HandbookPart;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class Handbook extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Handbook';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.handbook';

    public ?string $selectedPart = null;
    public ?int $selectedSection = null;

    public function getParts()
    {
        return HandbookPart::with('sections')->where('is_published', true)->orderBy('order')->get();
    }

    public function getSelectedPartModel(): ?HandbookPart
    {
        if (! $this->selectedPart) return null;
        return HandbookPart::with('sections')->where('code', $this->selectedPart)->first();
    }

    public function getSelectedSectionModel(): ?ContentSection
    {
        if (! $this->selectedSection) return null;
        return ContentSection::find($this->selectedSection);
    }

    public function selectPart(string $code): void
    {
        $this->selectedPart    = $code;
        $this->selectedSection = null;
    }

    public function selectSection(int $id): void
    {
        $this->selectedSection = $id;
    }

    public function back(): void
    {
        if ($this->selectedSection) {
            $this->selectedSection = null;
        } else {
            $this->selectedPart = null;
        }
    }

    public function getBodyHtml(): HtmlString
    {
        $section = $this->getSelectedSectionModel();
        return new HtmlString($section?->body ?? '');
    }
}
