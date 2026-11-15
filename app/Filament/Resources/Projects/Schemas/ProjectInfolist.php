<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('category.name')->label('Category'),
                        TextEntry::make('year'),
                        TextEntry::make('is_published')->label('Published')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('excerpt')->columnSpanFull(),
                        TextEntry::make('technologies.name')->badge()->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
