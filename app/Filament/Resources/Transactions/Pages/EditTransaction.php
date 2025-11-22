<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;
    
    protected ?string $originalType = null;
    protected array $originalItems = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->originalType = $this->record->type;
        
        $this->originalItems = $this->record->items()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $item->pivot->quantity,
                'current_stock' => $item->quantity,
            ];
        })->toArray();
        
        $data['transaction_items'] = $this->record->items->map(function ($item) {
            return [
                'item_id' => $item->id,
                'quantity' => $item->pivot->quantity,
            ];
        })->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['transaction_items']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $transaction = $this->record->fresh();
        
        $formData = $this->form->getState();
        $newItemsData = $formData['transaction_items'] ?? [];

        if (empty($newItemsData) && empty($this->originalItems)) {
            return;
        }

        DB::transaction(function () use ($transaction, $newItemsData) {
            foreach ($this->originalItems as $itemData) {
                $item = \App\Models\Item::find($itemData['id']);
                
                if (!$item) {
                    continue;
                }
                
                if ($this->originalType === 'assigned') {
                    $item->increment('quantity', $itemData['quantity']);
                } else {
                    $item->decrement('quantity', $itemData['quantity']);
                }
            }

            $transaction->items()->detach();

            foreach ($newItemsData as $itemData) {
                if (!isset($itemData['item_id']) || !isset($itemData['quantity'])) {
                    continue;
                }

                $transaction->items()->attach($itemData['item_id'], [
                    'quantity' => $itemData['quantity']
                ]);

                $item = \App\Models\Item::find($itemData['item_id']);
                
                if (!$item) {
                    continue;
                }
                
                if ($transaction->type === 'assigned') {
                    $item->decrement('quantity', $itemData['quantity']);
                } else {
                    $item->increment('quantity', $itemData['quantity']);
                }
            }
        });
    }
}