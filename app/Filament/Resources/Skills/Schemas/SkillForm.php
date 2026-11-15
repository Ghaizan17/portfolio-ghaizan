<?php

namespace App\Filament\Resources\Skills\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Skill')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        Select::make('skill_category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required()->maxLength(255),
                                Textarea::make('description')->rows(2),
                            ]),
                        Slider::make('percentage')
                            ->label('Self-assessed proficiency')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(5)
                            ->default(50)
                            ->helperText('Only record an honest, evidence-based estimate.'),
                        TextInput::make('sort_order')->numeric()->default(0)->required(),
                        Toggle::make('is_active')->default(true),
                    ])
                    ->columns(2),
                Section::make('Context')
                    ->schema([
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('What this skill is used for.'),
                        Textarea::make('evidence')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Concrete evidence from projects, coursework, or practice.'),
                        Select::make('project_id')
                            ->label('Related project')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
