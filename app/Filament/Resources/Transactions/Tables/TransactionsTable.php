<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.id')
                    ->searchable(),
                TextColumn::make('items')
                    ->label('Items')
                    ->formatStateUsing(function ($record) {
                        // Assume $record->items is a relation returning all items for the transaction,
                        // and the pivot table has a 'quantity' field.
                        if (!method_exists($record, 'items') && !property_exists($record, 'items')) {
                            return '-';
                        }
                        $items = $record->items;
                        if (blank($items)) {
                            return '-';
                        }
                        // Output: "ItemName1 (2), ItemName2 (5)"
                        return $items->map(function ($item) {
                            $qty = $item->pivot->quantity ?? null;
                            return $item->name . (isset($qty) ? " ({$qty})" : '');
                        })->implode(', ');
                    })
                    ->wrap()
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
