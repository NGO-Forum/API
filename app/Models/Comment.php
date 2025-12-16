<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'name',
        'email',
        'description',
        'images',
        'gender',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    protected $attributes = [
        'images' => '[]',
    ];
}
