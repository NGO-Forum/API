<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::with([
            'manager.person',
            'staff.person'
        ])->get();
    }

    public function show($id)
    {
        return Department::with([
            'manager.person',
            'staff.person'
        ])->findOrFail($id);
    }
}