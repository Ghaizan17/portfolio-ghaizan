<?php

namespace App\Filament\Resources\Messages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->copyable(),
                        TextEntry::make('subject')->columnSpanFull(),
                        TextEntry::make('message')->columnSpanFull(),
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('ip_address')->label('IP address'),
                        TextEntry::make('user_agent')->columnSpanFull()->limit(120),
                    ])
                    ->columns(2),
            ]);
    }
}
