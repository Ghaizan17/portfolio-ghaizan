<?php

namespace App\Filament\Widgets;

use App\Models\Message;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentMessages extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Message::query()->active()->latest())
            ->heading('Recent messages')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('subject')->limit(50),
                TextColumn::make('created_at')->since(),
            ])
            ->paginated([5]);
    }
}
