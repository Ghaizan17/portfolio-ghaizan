<?php

namespace App\Filament\Resources\Messages\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')->label('Read')->boolean(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('subject')->searchable()->limit(45),
                TextColumn::make('created_at')->dateTime()->sortable(),
                IconColumn::make('is_archived')->label('Archived')->boolean()->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_read')->label('Read'),
                TernaryFilter::make('is_archived')->label('Archived'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markRead')
                    ->label('Mark read')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record): bool => ! $record->is_read)
                    ->action(fn ($record) => $record->update(['is_read' => true])),
                Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->visible(fn ($record): bool => ! $record->is_archived)
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['is_archived' => true])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAllRead')
                        ->label('Mark read')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('archiveSelected')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_archived' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
