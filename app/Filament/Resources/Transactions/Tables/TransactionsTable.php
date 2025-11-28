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
            ->modifyQueryUsing(fn($query) => $query->with("items"))
            ->columns([
                TextColumn::make("member.full_name")
                    ->label("Член")
                    ->searchable(),
                TextColumn::make("items")
                    ->label("Items")
                    ->getStateUsing(
                        fn($record) => $record->items
                            ->map(
                                fn($item) => $item->name .
                                    " (" .
                                    $item->pivot->quantity .
                                    ")",
                            )
                            ->join(", "),
                    )
                    ->wrap()
                    ->searchable(
                        query: fn($query, $search) => $query->whereHas(
                            "items",
                            fn($q) => $q->where("name", "like", "%{$search}%"),
                        ),
                    ),
                TextColumn::make("type")
                    ->label("Статус")
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            "returned" => "Вратено",
                            "assigned" => "Задужено",
                            default => ucfirst($state),
                        },
                    )
                    ->color(
                        fn(string $state): string => match ($state) {
                            "assigned" => "danger",
                            "returned" => "success",
                            default => "gray",
                        },
                    )
                    ->icon(
                        fn(string $state): string => match ($state) {
                            "assigned"
                                => "heroicon-s-x-circle", // X icon for assigned (out of stock)
                            "returned"
                                => "heroicon-s-check-circle", // Checkmark icon for returned (in stock)
                            default => null,
                        },
                    ),
                TextColumn::make("transaction_date")
                    ->label("Дата на задавање")
                    ->date()
                    ->sortable(),
                TextColumn::make("user.name")
                    ->label("Креирано од")
                    ->searchable(),
                TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("deleted_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make("Return")
                    ->label("Врати")
                    ->button()
                    ->color("success")
                    ->icon("heroicon-s-arrow-up-right")
                    ->visible(fn($record): bool => $record->type === "assigned")
                    ->requiresConfirmation()
                    // 2. Action logic to update the record type AND inventory
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // 1. Update the transaction status
                            $record->update(["type" => "returned"]);

                            // 2. Return all associated items to stock (increment quantity)
                            foreach ($record->items as $item) {
                                // Find the Item model and update its stock
                                $itemModel = Item::find($item->id, ["*"]);

                                if ($itemModel) {
                                    $quantity = $item->pivot->quantity;
                                    $itemModel->increment(
                                        "quantity",
                                        $quantity,
                                    );
                                }
                            }
                        });
                    }),

                ActionGroup::make([ViewAction::make(), EditAction::make()]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
