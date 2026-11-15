<?php

namespace App\Filament\Resources\PostTags\Pages;

use App\Filament\Resources\PostTags\PostTagResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPostTag extends ViewRecord
{
    protected static string $resource = PostTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
