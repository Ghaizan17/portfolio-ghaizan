<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        FileUpload::make('avatar_path')
                            ->label('Profile photo')
                            ->image()
                            ->disk('public')
                            ->directory('profiles')
                            ->visibility('public')
                            ->maxSize((int) config('digital-dimension.uploads.max_image_kb'))
                            ->acceptedFileTypes(config('digital-dimension.uploads.image_mimes'))
                            ->avatar()
                            ->helperText('Square image recommended.'),
                        TextInput::make('full_name')->required()->maxLength(255),
                        TextInput::make('headline')
                            ->maxLength(255)
                            ->helperText('Example: Creative Full-Stack Developer.'),
                        TextInput::make('class')->maxLength(255),
                        TextInput::make('vocational_competency')->maxLength(255),
                        TextInput::make('location')->maxLength(255),
                        TextInput::make('availability')->maxLength(255),
                        TextInput::make('email')->email()->maxLength(255),
                        TextInput::make('phone')->tel()->maxLength(50),
                    ])
                    ->columns(2),
                Section::make('Biography')
                    ->schema([
                        Textarea::make('bio_short')
                            ->rows(3)
                            ->maxLength(600)
                            ->helperText('Used on the home page and in compact contexts.')
                            ->columnSpanFull(),
                        RichEditor::make('bio_long')
                            ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'h2', 'h3', 'blockquote', 'undo', 'redo'])
                            ->columnSpanFull(),
                        Textarea::make('personal_branding')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('career_goals')
                            ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'undo', 'redo'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
