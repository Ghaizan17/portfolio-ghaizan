<?php

namespace App\Filament\Resources\Technologies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TechnologyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
            ]);
    }
}
