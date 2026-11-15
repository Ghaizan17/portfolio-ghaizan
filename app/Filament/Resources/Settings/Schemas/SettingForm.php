<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Stable identifier used by templates. Changing it can break the site.'),
                Select::make('group')
                    ->options([
                        'general' => 'General',
                        'seo' => 'SEO',
                        'appearance' => 'Appearance',
                    ])
                    ->required()
                    ->native(false)
                    ->default('general'),
                Select::make('type')
                    ->options([
                        'string' => 'String',
                        'text' => 'Text',
                        'boolean' => 'Boolean',
                    ])
                    ->required()
                    ->native(false)
                    ->default('string'),
                Textarea::make('value')
                    ->rows(4)
                    ->columnSpanFull()
                    ->helperText('Use 1/0 for boolean values.'),
            ])
            ->columns(2);
    }
}
