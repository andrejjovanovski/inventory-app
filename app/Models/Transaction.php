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
        return $this->belongsToMany(Item::class, 'item_transaction')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
