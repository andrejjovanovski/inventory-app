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
        // Ensure pivot data is structured correctly if needed
        return $data;
    }

    protected function afterCreate(): void
    {
        $transaction = $this->record;

        DB::transaction(function () use ($transaction) {
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
