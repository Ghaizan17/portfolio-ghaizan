<?php

namespace App\Filament\Resources\Experiences\Tables;

use App\Models\Experience;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')->searchable()->limit(50),
                TextColumn::make('organization')->searchable(),
                TextColumn::make('position')->limit(30),
                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Experience::categories()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Experience::CATEGORY_INTERNSHIP => 'info',
                        Experience::CATEGORY_ORGANIZATION => 'success',
                        Experience::CATEGORY_COMMITTEE => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date()->sortable(),
                IconColumn::make('is_published')->label('Published')->boolean(),
                TextColumn::make('sort_order')->numeric()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')->options(Experience::categories()),
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([
                ViewAction::make(),
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
