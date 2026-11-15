<?php

namespace App\Filament\Resources\SocialLinks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SocialLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('platform')
                    ->options([
                        'email' => 'Email',
                        'github' => 'GitHub',
                        'instagram' => 'Instagram',
                        'linkedin' => 'LinkedIn',
                        'website' => 'Website',
                    ])
                    ->required()
                    ->searchable(),
                TextInput::make('label')->maxLength(255),
                TextInput::make('url')->required()->maxLength(255),
                Select::make('icon')
                    ->options([
                        'mail' => 'Mail',
                        'github' => 'GitHub',
                        'instagram' => 'Instagram',
                        'linkedin' => 'LinkedIn',
                        'globe' => 'Globe',
                    ])
                    ->native(false),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
                Toggle::make('is_active')->default(true),
            ])
            ->columns(2);
    }
}
