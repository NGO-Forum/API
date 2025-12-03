<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KhqrService;

class KhqrController extends Controller
{
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'full_name' => 'required|string|max:255',
        ]);

        $transactionId = "TXN-" . strtoupper(uniqid());

        $qr = (new KhqrService())->generate(
            $validated['amount'],
            $transactionId
        );

        return response()->json([
            'success'     => true,
            'qr'          => $qr,
            'transaction' => $transactionId,
        ]);
    }
}

