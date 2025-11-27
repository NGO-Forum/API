<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','role','position','email','img','description','education','category', 'phone'
    ];

    protected $casts = [
        'education' => 'array',
    ];

    public function departmentManager()
    {
        return $this->hasOne(DepartmentManager::class, 'person_id');
    }

    public function departmentStaff()
    {
        return $this->hasOne(DepartmentStaff::class, 'person_id');
    }
}
