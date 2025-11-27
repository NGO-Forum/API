<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentManager extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'person_id'];

    public function person()
    {
        return $this->belongsTo(People::class, 'person_id');
    }
}
