<?php

namespace App\Filament\Resources\GuestbookEntries\Tables;

use App\Models\GuestbookEntry;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class GuestbookEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('message')->limit(70)->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        GuestbookEntry::STATUS_APPROVED => 'success',
                        GuestbookEntry::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    GuestbookEntry::STATUS_PENDING => 'Pending',
                    GuestbookEntry::STATUS_APPROVED => 'Approved',
                    GuestbookEntry::STATUS_REJECTED => 'Rejected',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (GuestbookEntry $record): bool => $record->status !== GuestbookEntry::STATUS_APPROVED)
                    ->action(fn (GuestbookEntry $record) => $record->update(['status' => GuestbookEntry::STATUS_APPROVED])),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (GuestbookEntry $record): bool => $record->status !== GuestbookEntry::STATUS_REJECTED)
                    ->requiresConfirmation()
                    ->action(fn (GuestbookEntry $record) => $record->update(['status' => GuestbookEntry::STATUS_REJECTED])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => GuestbookEntry::STATUS_APPROVED]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('rejectSelected')
                        ->label('Reject')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => GuestbookEntry::STATUS_REJECTED]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
