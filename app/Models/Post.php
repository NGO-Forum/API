<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'department',
        'images',
        'description',
        'published_at',
        'file',
        'link',
    ];

    protected $casts = [
        'images' => 'array', // Cast JSON to array
    ];

    // FULL URL for file (PDF)
    public function getFileUrlAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    // FULL URLs for images
    public function getImageUrlsAttribute()
    {
        if (!$this->images) return [];

        return array_map(function ($img) {
            return asset('storage/' . $img);
        }, $this->images);
    }
}
