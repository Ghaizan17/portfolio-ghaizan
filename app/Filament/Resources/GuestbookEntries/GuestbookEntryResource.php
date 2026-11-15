<?php

namespace App\Filament\Resources\GuestbookEntries;

use App\Filament\Resources\GuestbookEntries\Pages\EditGuestbookEntry;
use App\Filament\Resources\GuestbookEntries\Pages\ListGuestbookEntries;
use App\Filament\Resources\GuestbookEntries\Schemas\GuestbookEntryForm;
use App\Filament\Resources\GuestbookEntries\Tables\GuestbookEntriesTable;
use App\Models\GuestbookEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GuestbookEntryResource extends Resource
{
    protected static ?string $model = GuestbookEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftEllipsis;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Guestbook';

    protected static ?string $modelLabel = 'guestbook entry';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GuestbookEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuestbookEntriesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = GuestbookEntry::query()->where('status', GuestbookEntry::STATUS_PENDING)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuestbookEntries::route('/'),
            'edit' => EditGuestbookEntry::route('/{record}/edit'),
        ];
    }
}
