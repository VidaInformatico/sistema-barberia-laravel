<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'last_name',
    'slug',
    'email',
    'password',
    'phone',
    'role',
    'is_active',
    'avatar',
    'branch_id',
    'bluetooth_printing',
    'bluetooth_print_size',
    'direct_pc_printing',
    'pc_print_size',
    'pc_printer_name'
])]

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset("storage/{$this->avatar}")
            : "https://ui-avatars.com/api/?name={$this->name}+{$this->last_name}&background=random";
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Role helpers                                                        */
    /* ------------------------------------------------------------------ */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isBarber(): bool
    {
        return $this->role === 'barber';
    }
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                       */
    /* ------------------------------------------------------------------ */

    public function barber(): HasOne
    {
        return $this->hasOne(Barber::class);
    }

    /** Reservas como cliente */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'client_id');
    }
}
