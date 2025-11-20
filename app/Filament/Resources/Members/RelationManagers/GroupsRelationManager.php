<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Filament\Resources\Groups\GroupResource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $relatedResource = GroupResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->headerActions([
                AttachAction::make()->preloadRecordSelect(), // attach existing groups
            ])
            ->recordActions([
                DetachAction::make(), // detach from member
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
