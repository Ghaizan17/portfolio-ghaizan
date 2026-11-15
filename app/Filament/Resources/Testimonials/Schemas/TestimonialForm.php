<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Models\Testimonial;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Person')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('position')->maxLength(255),
                        TextInput::make('organization')->maxLength(255),
                        Select::make('relationship')
                            ->options(Testimonial::relationships())
                            ->required()
                            ->native(false),
                        Select::make('project_id')
                            ->label('Related project')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload(),
                        FileUpload::make('photo_path')
                            ->label('Profile photo')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('testimonials')
                            ->visibility('public')
                            ->maxSize((int) config('digital-dimension.uploads.max_image_kb'))
                            ->acceptedFileTypes(config('digital-dimension.uploads.image_mimes')),
                    ])
                    ->columns(2),
                Section::make('Testimonial')
                    ->schema([
                        Textarea::make('content')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Publish only authentic testimonials with the author\'s permission.'),
                        Toggle::make('is_published')->label('Published')->default(false),
                        TextInput::make('sort_order')->numeric()->default(0)->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
