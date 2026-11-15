<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TestimonialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('position')
                    ->placeholder('-'),
                TextEntry::make('organization')
                    ->placeholder('-'),
                TextEntry::make('relationship'),
                TextEntry::make('content')
                    ->columnSpanFull(),
                TextEntry::make('photo_path')
                    ->placeholder('-'),
                TextEntry::make('project.title')
                    ->label('Project')
                    ->placeholder('-'),
                IconEntry::make('is_published')
                    ->boolean(),
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
