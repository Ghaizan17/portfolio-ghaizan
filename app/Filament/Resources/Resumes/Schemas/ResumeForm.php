<?php

namespace App\Filament\Resources\Resumes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResumeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('CV file')
                    ->schema([
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Shown on the resume page and used for the download filename.'),
                        FileUpload::make('file_path')
                            ->label('PDF file')
                            ->required()
                            ->disk('local')
                            ->directory('resumes')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize((int) config('digital-dimension.uploads.max_document_kb'))
                            ->helperText('Stored privately and streamed through the resume download route.'),
                        Toggle::make('is_active')
                            ->label('Active CV')
                            ->helperText('Only one CV can be active. Activating this record deactivates the others.'),
                        DateTimePicker::make('uploaded_at')
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
