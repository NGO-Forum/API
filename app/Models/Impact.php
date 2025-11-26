<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'summary',
        'logo',     // json array of paths
        'file',     // single file path
        'donors',
        'images', 
        'program',  // json array of paths
        'project_id',
    ];

    protected $casts = [
        'images' => 'array',
        'logo'   => 'array', // multiple logos
        'program' => 'array',
    ];

    protected $appends = ['logo_urls', 'image_urls', 'file_url'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getLogoUrlsAttribute()
    {
        if (!$this->logo || !is_array($this->logo)) return [];
        return array_map(fn($p) => asset('storage/' . $p), $this->logo);
    }

    public function getImageUrlsAttribute()
    {
        if (!$this->images || !is_array($this->images)) return [];
        return array_map(fn($p) => asset('storage/' . $p), $this->images);
    }

    public function getFileUrlAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }
}
