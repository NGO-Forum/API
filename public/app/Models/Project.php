<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'summary',
        'objectives',
        'duration_start',
        'duration_end',
        'target_areas',
        'target_groups',
        'donor',
        'key_activities',
        'images',
        'department',
    ];

    protected $casts = [
        'objectives'    => 'array',
        'key_activities' => 'array',
        'duration_start' => 'date',
        'duration_end'  => 'date',
        'images'  => 'array',
    ];

    protected $appends = ['image_urls'];

    public function getImageUrlsAttribute()
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        return array_map(function ($img) {
            return asset('storage/' . $img);
        }, $this->images);
    }
}
