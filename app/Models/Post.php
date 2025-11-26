<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'images',
        'description',
        'published_at',
        'department',
        'link',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'images' => 'array',
    ];
}
