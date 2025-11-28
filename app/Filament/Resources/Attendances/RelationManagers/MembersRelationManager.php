<?php

namespace App\Filament\Resources\Attendances\RelationManagers;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;


class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';
    protected static ?string $recordTitleAttribute = 'full_name';


    protected static ?string $relatedResource = MemberResource::class;


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label('Member'),
                ToggleColumn::make('pivot.is_present')
                    ->label('Present')
                    ->afterStateUpdated(function ($record, $state) {
                        $record->pivot->update(['is_present' => $state]);
                    }),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $groupId = $this->ownerRecord->group_id; // make sure Attendance has group_id

        return Member::query()
            ->whereHas('groups', function ($query) use ($groupId) {
                $query->where('groups.id', $groupId);
            });
    }
}
