<?php

namespace App\Filament\Resources\StaffDirectoryResource\Pages;

use App\Filament\Resources\StaffDirectoryResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewStaff extends ViewRecord
{
    protected static string $resource = StaffDirectoryResource::class;

    protected function getHeaderActions(): array { return []; }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()->schema([
                Infolists\Components\ImageEntry::make('avatar_path')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=533afd&background=e5e3ff')
                    ->width(80)->height(80),
                Infolists\Components\TextEntry::make('name')->size('xl'),
                Infolists\Components\TextEntry::make('job_title'),
            ])->columns(3),

            Infolists\Components\Section::make('Organisation')->schema([
                Infolists\Components\TextEntry::make('unit.department.name')->label('Department'),
                Infolists\Components\TextEntry::make('unit.name')->label('Unit'),
                Infolists\Components\TextEntry::make('level.name')->label('Level'),
                Infolists\Components\TextEntry::make('superior.name')->label('Reports to'),
                Infolists\Components\TextEntry::make('join_date')->date('d M Y'),
                Infolists\Components\TextEntry::make('phone'),
            ])->columns(2),

            Infolists\Components\Section::make('Personality')->schema([
                Infolists\Components\TextEntry::make('id')
                    ->label('')
                    ->state('Personality results will appear here in Phase 5.')
                    ->color('gray'),
            ]),

            Infolists\Components\Section::make('KPI History')->schema([
                Infolists\Components\TextEntry::make('id')
                    ->label('')
                    ->state('KPI reviews will appear here in Phase 6.')
                    ->color('gray'),
            ]),
        ]);
    }
}
