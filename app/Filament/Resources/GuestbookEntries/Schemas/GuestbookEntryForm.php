<?php

namespace App\Filament\Resources\GuestbookEntries\Schemas;

use App\Models\GuestbookEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GuestbookEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->maxLength(255),
                Textarea::make('message')->required()->rows(5)->columnSpanFull(),
                Select::make('status')
                    ->options([
                        GuestbookEntry::STATUS_PENDING => 'Pending moderation',
                        GuestbookEntry::STATUS_APPROVED => 'Approved',
                        GuestbookEntry::STATUS_REJECTED => 'Rejected',
                    ])
                    ->required()
                    ->native(false),
            ])
            ->columns(2);
    }
}
