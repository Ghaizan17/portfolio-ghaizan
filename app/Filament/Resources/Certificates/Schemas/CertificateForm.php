<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Certificate')
                    ->schema([
                        TextInput::make('title')->required()->maxLength(255),
                        TextInput::make('issuer')->required()->maxLength(255),
                        DatePicker::make('issued_at')->native(false),
                        TextInput::make('category')->maxLength(255)->helperText('For example: Course, Competition, Workshop.'),
                        TextInput::make('sort_order')->numeric()->default(0)->required(),
                        Toggle::make('is_published')->default(true),
                    ])
                    ->columns(2),
                Textarea::make('description')->rows(4)->columnSpanFull(),
                Section::make('Certificate file')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Certificate image or PDF')
                            ->disk('public')
                            ->directory('certificates')
                            ->visibility('public')
                            ->acceptedFileTypes(array_merge(
                                config('digital-dimension.uploads.image_mimes'),
                                config('digital-dimension.uploads.document_mimes'),
                            ))
                            ->maxSize((int) config('digital-dimension.uploads.max_document_kb')),
                        Select::make('file_type')
                            ->options(['image' => 'Image', 'pdf' => 'PDF'])
                            ->native(false)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Detected automatically from the uploaded file extension.'),
                    ])
                    ->columns(2),
            ]);
    }
}
