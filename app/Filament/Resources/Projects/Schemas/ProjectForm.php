<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Project')
                    ->tabs([
                        Tab::make('Overview')
                            ->schema([
                                Section::make('Core details')
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        TextInput::make('slug')
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Leave blank to generate from the title.'),
                                        Select::make('project_category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')->required()->maxLength(255),
                                                Textarea::make('description'),
                                            ]),
                                        TextInput::make('year')
                                            ->numeric()
                                            ->minValue(2000)
                                            ->maxValue((int) date('Y') + 1),
                                        TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0)
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Textarea::make('excerpt')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold', 'italic', 'link', 'bulletList', 'orderedList',
                                        'h2', 'h3', 'blockquote', 'codeBlock', 'undo', 'redo',
                                    ]),
                                Section::make('Publishing')
                                    ->schema([
                                        Toggle::make('featured')->label('Featured project'),
                                        Toggle::make('is_published')->label('Published'),
                                        DateTimePicker::make('published_at')
                                            ->helperText('Leave blank to publish immediately when marked published.'),
                                    ])
                                    ->columns(3),
                            ]),
                        Tab::make('Case study')
                            ->schema([
                                RichEditor::make('problem_statement')
                                    ->label('Problem statement')
                                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'undo', 'redo']),
                                RichEditor::make('objectives')
                                    ->label('Objectives')
                                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'undo', 'redo']),
                                TextInput::make('role')
                                    ->maxLength(255)
                                    ->helperText('Your role on this project.'),
                                Textarea::make('team_collaboration')
                                    ->rows(3)
                                    ->helperText('How the team worked together, if applicable.'),
                            ]),
                        Tab::make('Links & SEO')
                            ->schema([
                                TextInput::make('demo_url')->url()->maxLength(255),
                                TextInput::make('github_url')->url()->maxLength(255),
                                TextInput::make('meta_title')->maxLength(255),
                                Textarea::make('meta_description')->rows(3)->maxLength(500),
                            ]),
                        Tab::make('Media & stack')
                            ->schema([
                                FileUpload::make('cover_path')
                                    ->label('Cover image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('projects/covers')
                                    ->visibility('public')
                                    ->maxSize((int) config('digital-dimension.uploads.max_image_kb'))
                                    ->acceptedFileTypes(config('digital-dimension.uploads.image_mimes'))
                                    ->imageEditor()
                                    ->helperText('Recommended: 1600×1000 or wider. WebP or JPEG.'),
                                Select::make('technologies')
                                    ->relationship('technologies', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Manage the technology list under Portfolio → Technologies.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
