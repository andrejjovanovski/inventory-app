<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasSuperAdmin;


    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@kudsrpskivez.com');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ["name", "email", "password"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function created_members(): HasMany
    {
        return $this->hasMany(Member::class, "created_by");
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, "created_by");
    }

    public function mentorAttendance(): HasMany
    {
        return $this->hasMany(Attendance::class, "mentor_id");
    }
}
