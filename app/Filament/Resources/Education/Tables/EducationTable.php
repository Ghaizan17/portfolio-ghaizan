<?php

namespace App\Filament\Resources\Education\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EducationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('institution')->searchable(),
                TextColumn::make('degree')->searchable(),
                TextColumn::make('level'),
                TextColumn::make('start_year')->sortable(),
                TextColumn::make('end_year')->sortable(),
                IconColumn::make('is_current')->label('Current')->boolean(),
                TextColumn::make('sort_order')->numeric()->sortable(),
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
