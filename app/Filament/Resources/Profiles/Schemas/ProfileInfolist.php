<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('full_name'),
                TextEntry::make('headline')
                    ->placeholder('-'),
                TextEntry::make('class')
                    ->placeholder('-'),
                TextEntry::make('vocational_competency')
                    ->placeholder('-'),
                TextEntry::make('bio_short')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('bio_long')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('personal_branding')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('career_goals')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('location')
                    ->placeholder('-'),
                TextEntry::make('availability')
                    ->placeholder('-'),
                TextEntry::make('avatar_path')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
