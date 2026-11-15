<?php

namespace App\Filament\Resources\Education\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EducationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('institution')->required()->maxLength(255),
                TextInput::make('degree')->maxLength(255),
                TextInput::make('field_of_study')->maxLength(255),
                TextInput::make('level')->maxLength(255),
                TextInput::make('start_year')->numeric()->required()->minValue(1900)->maxValue((int) date('Y')),
                TextInput::make('end_year')->numeric()->minValue(1900)->maxValue((int) date('Y') + 10),
                Toggle::make('is_current')->label('Currently studying'),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ])
            ->columns(2);
    }
}
