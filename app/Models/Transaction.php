<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'transaction_date',
        'notes',
        'user_id',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        // CREATE: adjust quantities when transaction is created
        static::created(function ($transaction) {
            DB::transaction(function () use ($transaction) {
                foreach ($transaction->items as $item) {
                    if ($transaction->type === 'assigned') {
                        $item->decrement('quantity', $item->pivot->quantity);
                    } else {
                        $item->increment('quantity', $item->pivot->quantity);
                    }
                }
            });
        });

        // UPDATE: revert old quantities before applying new ones
        static::updating(function ($transaction) {
            DB::transaction(function () use ($transaction) {

                // Get original type
                $originalType = $transaction->getOriginal('type');

                // Load original pivot quantities
                $originalItems = $transaction->items()->get();

                // Revert old quantities
                foreach ($originalItems as $item) {
                    if ($originalType === 'assigned') {
                        $item->increment('quantity', $item->pivot->quantity);
                    } else {
                        $item->decrement('quantity', $item->pivot->quantity);
                    }
                }
            });
        });

        // UPDATE: apply new quantities after saving
        static::updated(function ($transaction) {
            DB::transaction(function () use ($transaction) {
                foreach ($transaction->items as $item) {
                    if ($transaction->type === 'assigned') {
                        $item->decrement('quantity', $item->pivot->quantity);
                    } else {
                        $item->increment('quantity', $item->pivot->quantity);
                    }
                }
            });
        });

        // DELETE: revert quantities when transaction is deleted
        static::deleting(function ($transaction) {
            DB::transaction(function () use ($transaction) {
                foreach ($transaction->items as $item) {
                    if ($transaction->type === 'assigned') {
                        $item->increment('quantity', $item->pivot->quantity);
                    } else {
                        $item->decrement('quantity', $item->pivot->quantity);
                    }
                }
            });
        });
    }
}
