<?php

namespace App\Filament\Resources\Profiles\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->oldest('id'))
            ->columns([
                ImageColumn::make('avatar_path')->label('Photo')->disk('public')->circular()->size(40),
                TextColumn::make('full_name')->searchable()->sortable(),
                TextColumn::make('headline')->limit(50),
                TextColumn::make('class'),
                TextColumn::make('vocational_competency'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ]);
    }
}
