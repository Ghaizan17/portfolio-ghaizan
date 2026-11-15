<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Models\Experience;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Experience')
                    ->schema([
                        TextInput::make('title')->required()->maxLength(255),
                        TextInput::make('organization')->required()->maxLength(255),
                        TextInput::make('position')->maxLength(255),
                        Select::make('category')
                            ->options(Experience::categories())
                            ->required()
                            ->native(false),
                        DatePicker::make('start_date')->required()->native(false),
                        DatePicker::make('end_date')->native(false),
                        Toggle::make('is_current')->label('Currently active'),
                        Toggle::make('is_published')->label('Published')->default(true),
                        TextInput::make('sort_order')->numeric()->default(0)->required(),
                    ])
                    ->columns(2),
                Section::make('Details')
                    ->schema([
                        Textarea::make('description')->rows(4)->columnSpanFull(),
                        Textarea::make('responsibilities')
                            ->rows(4)
                            ->helperText('One responsibility per line.')
                            ->columnSpanFull(),
                        Textarea::make('achievements')
                            ->rows(4)
                            ->helperText('Only document achievements you can support with evidence.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
