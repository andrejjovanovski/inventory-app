<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function afterSave(): void
    {
        $transaction = $this->record;

        DB::transaction(function () use ($transaction) {
            // Revert old quantities first
            $originalType = $transaction->getOriginal('type');
            $originalItems = $transaction->items()->get();

            foreach ($originalItems as $item) {
                if ($originalType === 'assigned') {
                    $item->increment('quantity', $item->pivot->quantity);
                } else {
                    $item->decrement('quantity', $item->pivot->quantity);
                }
            }

            // Apply new quantities
            foreach ($transaction->items as $item) {
                if ($transaction->type === 'assigned') {
                    $item->decrement('quantity', $item->pivot->quantity);
                } else {
                    $item->increment('quantity', $item->pivot->quantity);
                }
            }
        });
    }
}
