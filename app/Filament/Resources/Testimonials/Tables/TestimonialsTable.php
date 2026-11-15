<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('photo_path')->label('Photo')->disk('public')->circular()->size(40),
                TextColumn::make('name')->searchable(),
                TextColumn::make('organization')->searchable()->limit(30),
                TextColumn::make('relationship')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Testimonial::relationships()[$state] ?? $state),
                TextColumn::make('content')->limit(60),
                ToggleColumn::make('is_published')->label('Published'),
                TextColumn::make('sort_order')->numeric()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('relationship')->options(Testimonial::relationships()),
                SelectFilter::make('is_published')
                    ->label('Publication')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Unpublished',
                    ]),
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
