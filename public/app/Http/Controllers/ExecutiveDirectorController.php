<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExecutiveDirector;

class ExecutiveDirectorController extends Controller
{
    public function show()
    {
        return ExecutiveDirector::with('person')->first();
    }
}
