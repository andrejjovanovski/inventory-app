<?php

namespace App\Filament\Resources\Attendances\RelationManagers;

use App\Filament\Resources\Members\MemberResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $recordTitleAttribute = 'full_name';

    // This allows you to click a member name and jump to their profile
    protected static ?string $relatedResource = MemberResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Member')
                    ->sortable()
                    ->searchable(),

                ToggleColumn::make('attendance_present')
                    ->label('Present')
                    ->getStateUsing(fn ($record): bool => (bool) $record->pivot?->is_present)
                    ->updateStateUsing(fn ($record, $state) => $record->pivot->update([
                        'is_present' => $state
                    ])),
            ])
            
            ->filters([])
            ->headerActions([]) 
            ->recordActions([])
            ->toolbarActions([]);
    }
}