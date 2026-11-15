<?php

namespace App\Filament\Resources\Education\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EducationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('institution'),
                TextEntry::make('degree')
                    ->placeholder('-'),
                TextEntry::make('field_of_study')
                    ->placeholder('-'),
                TextEntry::make('level')
                    ->placeholder('-'),
                TextEntry::make('start_year')
                    ->numeric(),
                TextEntry::make('end_year')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_current')
                    ->boolean(),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
