<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display a listing of donations.
     */
    public function index()
    {
        return response()->json(Donation::latest()->get());
    }

    /**
     * Store a newly created donation (before payment).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'nullable|string|max:20',
            'amount'    => 'required|numeric|min:1',
            'message'   => 'nullable|string',
        ]);

        // Generate transaction unique ID
        $validated['transaction'] = 'TXN-' . strtoupper(uniqid());

        $donation = Donation::create($validated);

        return response()->json([
            'success' => true,
            'data' => $donation,
        ], 201);
    }

    /**
     * Mark donation as paid (for after payment confirmation).
     */
    public function markPaid(Request $request, $transaction)
    {
        $donation = Donation::where('transaction', $transaction)->firstOrFail();

        $donation->update([
            'paid' => true,
            'paid_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donation marked as paid.',
            'data' => $donation,
        ]);
    }
}
