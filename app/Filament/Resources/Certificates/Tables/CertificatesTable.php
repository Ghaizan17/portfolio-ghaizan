<?php

namespace App\Filament\Resources\Certificates\Tables;

use App\Models\Certificate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('media'))
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('preview_path')
                    ->label('Preview')
                    ->disk('public')
                    ->square()
                    ->size(44)
                    ->defaultImageUrl(fn (Certificate $record): ?string => $record->is_pdf
                        ? asset('images/pdf-preview.svg')
                        : null),
                TextColumn::make('title')->searchable()->limit(50),
                TextColumn::make('issuer')->searchable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('issued_at')->date()->sortable(),
                IconColumn::make('is_published')->label('Published')->boolean(),
                TextColumn::make('sort_order')->numeric()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(fn (): array => Certificate::query()
                        ->whereNotNull('category')
                        ->distinct()
                        ->pluck('category', 'category')
                        ->all()),
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
