<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->maxLength(255)->unique(ignoreRecord: true),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
            ])
            ->columns(2);
    }
}
