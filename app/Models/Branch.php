<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'active',
        'direct_printing',
        'printer_name',
        'print_size'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
