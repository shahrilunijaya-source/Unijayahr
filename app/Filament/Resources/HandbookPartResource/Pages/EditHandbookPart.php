<?php

namespace App\Filament\Resources\HandbookPartResource\Pages;

use App\Filament\Resources\HandbookPartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHandbookPart extends EditRecord
{
    protected static string $resource = HandbookPartResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
