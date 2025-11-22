<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Item;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB; // <-- Import DB

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.full_name')
                    ->searchable(),
                TextColumn::make('items')
                    ->label('Items')
                    ->formatStateUsing(function ($record) {
                        if (! method_exists($record, 'items') && ! property_exists($record, 'items')) {
                            return '-';
                        }
                        
                        $items = $record->items;
                        
                        if (blank($items)) {
                            return '-';
                        }

                        $uniqueItems = $items->unique('id');

                        // Output: "ItemName1 (2), ItemName2 (5)"
                        return $uniqueItems->map(function ($item) {
                            $qty = $item->pivot->quantity ?? null;

                            return $item->name.(isset($qty) ? " ({$qty})" : '');
                        })->implode(', ');
                    })
                    ->wrap()
                    ->searchable(),
                TextColumn::make('type')
                    ->label("Статус")
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'returned' => 'Вратено',
                        'assigned' => 'Задужено',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'danger',
                        'returned' => 'success',
                        default => 'gray',
                    })
                        ->icon(fn (string $state): string => match ($state) {
                            'assigned' => 'heroicon-s-x-circle', // X icon for assigned (out of stock)
                            'returned' => 'heroicon-s-check-circle', // Checkmark icon for returned (in stock)
                            default => null,
                        }),
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
                Action::make("Return")
                    ->label('Врати')
                    ->button()
                    ->color('success') 
                    ->icon('heroicon-s-arrow-up-right')
                    ->visible(fn ($record): bool => $record->type === 'assigned')
                    ->requiresConfirmation()
                    // 2. Action logic to update the record type AND inventory
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // 1. Update the transaction status
                            $record->update(['type' => 'returned']);

                            // 2. Return all associated items to stock (increment quantity)
                            foreach ($record->items as $item) {
                                // Find the Item model and update its stock
                                $itemModel = Item::find($item->id);
                                
                                if ($itemModel) {
                                    $quantity = $item->pivot->quantity;
                                    $itemModel->increment('quantity', $quantity);
                                }
                            }
                        });
                    }),
                
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ])
               
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}