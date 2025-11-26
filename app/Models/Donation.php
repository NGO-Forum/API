<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'amount',
        'message',
        'transaction',
        'paid',
        'paid_at',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];
}
