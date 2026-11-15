<?php

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Experience')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('organization'),
                        TextEntry::make('position'),
                        TextEntry::make('category'),
                        TextEntry::make('description')->columnSpanFull(),
                        TextEntry::make('responsibilities')->columnSpanFull(),
                        TextEntry::make('achievements')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
