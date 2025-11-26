<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function manager()
    {
        return $this->hasOne(DepartmentManager::class);
    }

    public function staff()
    {
        return $this->hasMany(DepartmentStaff::class);
    }
}
