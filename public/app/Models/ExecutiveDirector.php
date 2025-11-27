<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutiveDirector extends Model
{
    use HasFactory;

    protected $table = 'executive_director';

    public function person()
    {
        return $this->belongsTo(People::class, 'person_id');
    }
}
