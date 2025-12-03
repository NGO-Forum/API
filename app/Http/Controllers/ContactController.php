<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email',
            'phone'      => 'nullable',
            'message'    => 'required',
        ]);

        // User full name
        $name = $request->first_name . ' ' . $request->last_name;

        // Build email content (looks like user wrote it)
        $html = "
            <p>Dear NGO Forum Team,</p>

            <p>My name is <strong>{$name}</strong>.</p>

            <p><strong>Email:</strong> {$request->email}<br>
            <strong>Phone:</strong> {$request->phone}</p>

            <p><strong>Message:</strong></p>
            <p style='white-space:pre-line;'>"
                . nl2br(e($request->message)) .
            "</p>

            <br>
            <p>Thank you,<br>
            {$name}</p>
        ";

        Mail::send([], [], function ($message) use ($request, $name, $html) {
            $message->to("info@ngoforum.org.kh")

                // User is the sender (looks natural)
                ->from($request->email, $name)

                // Safe fallback: website also added
                ->replyTo($request->email, $name)

                ->subject("New Contact Message – {$name}")
                ->html($html);
        });

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully!'
        ]);
    }
}
