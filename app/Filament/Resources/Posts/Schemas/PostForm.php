<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Article')
                    ->tabs([
                        Tab::make('Content')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Leave blank to generate from the title.'),
                                Select::make('post_category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->required()->maxLength(255),
                                        Textarea::make('description')->rows(2),
                                    ]),
                                Select::make('user_id')
                                    ->label('Author')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => auth()->id()),
                                Select::make('tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->required()->maxLength(255),
                                    ]),
                                Textarea::make('excerpt')->required()->rows(3)->maxLength(500)->columnSpanFull(),
                                RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold', 'italic', 'link', 'bulletList', 'orderedList',
                                        'h2', 'h3', 'blockquote', 'codeBlock', 'table',
                                        'undo', 'redo',
                                    ]),
                            ])
                            ->columns(2),
                        Tab::make('Media & SEO')
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->label('Article thumbnail')
                                    ->image()
                                    ->disk('public')
                                    ->directory('journal')
                                    ->visibility('public')
                                    ->maxSize((int) config('digital-dimension.uploads.max_image_kb'))
                                    ->acceptedFileTypes(config('digital-dimension.uploads.image_mimes')),
                                TextInput::make('meta_title')->maxLength(255),
                                Textarea::make('meta_description')->rows(3)->maxLength(500),
                            ]),
                        Tab::make('Publishing')
                            ->schema([
                                Toggle::make('is_published')->label('Published'),
                                DateTimePicker::make('published_at')
                                    ->helperText('Future dates keep the article hidden until that time.'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
