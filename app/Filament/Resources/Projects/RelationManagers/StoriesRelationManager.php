<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'stories';

    protected static ?string $title = 'Development story';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->helperText('For example: Initial idea, Research and planning, Debugging and solutions.'),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold', 'italic', 'link', 'bulletList', 'orderedList',
                        'h2', 'h3', 'blockquote', 'codeBlock', 'undo', 'redo',
                    ]),
                FileUpload::make('image_path')
                    ->label('Supporting image')
                    ->image()
                    ->disk('public')
                    ->directory('projects/documentation')
                    ->visibility('public')
                    ->maxSize((int) config('digital-dimension.uploads.max_image_kb'))
                    ->acceptedFileTypes(config('digital-dimension.uploads.image_mimes'))
                    ->columnSpanFull(),
                Textarea::make('code_snippet')
                    ->label('Code snippet (optional)')
                    ->rows(6)
                    ->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('sort_order')->numeric()->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
