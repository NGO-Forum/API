<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkFile extends Model
{
    use HasFactory;

    protected $fillable = ['network_id', 'title', 'file_url'];

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
