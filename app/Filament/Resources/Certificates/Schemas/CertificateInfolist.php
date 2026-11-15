<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Models\Certificate;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Certificate')
                    ->schema([
                        ImageEntry::make('preview_path')
                            ->label('Preview')
                            ->disk('public')
                            ->imageHeight(120)
                            ->defaultImageUrl(fn (Certificate $record): ?string => $record->is_pdf
                                ? asset('images/pdf-preview.svg')
                                : null),
                        TextEntry::make('title'),
                        TextEntry::make('issuer'),
                        TextEntry::make('issued_at')->date(),
                        TextEntry::make('category'),
                        TextEntry::make('description')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
