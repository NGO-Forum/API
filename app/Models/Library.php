<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'year',
        'file_kh',
        'file_en',
        'thumbnail',
    ];

    protected $appends = ['file_kh_url', 'file_en_url'];

    public function getFileKhUrlAttribute()
    {
        return $this->file_kh ? asset('storage/' . $this->file_kh) : null;
    }

    public function getFileEnUrlAttribute()
    {
        return $this->file_en ? asset('storage/' . $this->file_en) : null;
    }
}
