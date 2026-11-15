<?php

namespace App\Filament\Resources\GuestbookEntries\Pages;

use App\Filament\Resources\GuestbookEntries\GuestbookEntryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGuestbookEntry extends ViewRecord
{
    protected static string $resource = GuestbookEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
