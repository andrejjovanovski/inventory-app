<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove transaction_items from main data
        unset($data['transaction_items']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $transaction = $this->record;
        $itemsData = $this->data['transaction_items'] ?? [];

        DB::transaction(function () use ($transaction, $itemsData) {
            // Attach items with quantities
            foreach ($itemsData as $itemData) {
                $transaction->items()->attach($itemData['item_id'], [
                    'quantity' => $itemData['quantity']
                ]);

                // Update item stock
                $item = \App\Models\Item::find($itemData['item_id']);
                if ($transaction->type === 'assigned') {
                    $item->decrement('quantity', $itemData['quantity']);
                } else {
                    $item->increment('quantity', $itemData['quantity']);
                }
            }
        });
    }
}