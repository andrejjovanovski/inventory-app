<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "full_name",
        "national_id",
        "passport_number",
        "passport_expiration_date",
        "is_email_verified",
        "date_of_birth",
        "gender",
        "parent_name",
        "address",
        "phone_number",
        "email",
        "embg",
        "is_active",
        "created_by",
    ];

    protected $casts = [
        "embg" => "encrypted",
        "passport_number" => "encrypted",
        "national_id" => "encrypted",
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            Group::class,
            "group_member",
            "member_id",
            "group_id",
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->withPivot("joined_at", "status")
            ->withTimestamps();
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
            "transactions",
            "member_id",
            "item_id",
        )
            ->withPivot(
                "quantity",
                "type",
                "transaction_date",
                "notes",
                "user_id",
            )
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
