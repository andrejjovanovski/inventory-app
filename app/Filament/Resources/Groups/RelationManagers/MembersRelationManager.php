<?php

namespace App\Filament\Resources\Groups\RelationManagers;

use App\Filament\Resources\Groups\GroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $relatedResource = GroupResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Date of Birth')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent_name')
                    ->label('Parent Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('#')
                    ->label('Personal ID')
                    ->searchable()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
