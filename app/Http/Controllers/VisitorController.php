<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;

class VisitorController extends Controller
{
    public function track(Request $request)
    {
        $ip = $request->ip(); // get user IP

        // Save only once — don't duplicate IPs
        Visitor::firstOrCreate(['ip' => $ip]);

        return response()->json(['success' => true, 'ip' => $ip]);
    }

    public function count()
    {
        return response()->json([
            'unique_visitors' => Visitor::count()
        ]);
    }
}
