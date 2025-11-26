<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class JobApplicationController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'job_title' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'cv' => 'nullable|file|mimes:pdf|max:4096',
        ]);

        // Format Message in a clean HTML Template
        $html = '
            <h2 style="color:#1b8b4c;">Job Application</h2>

            <p>Dear Hiring Manager,</p>

            <p>
                I hope this message finds you well.<br>
                My name is <strong>' . htmlentities($request->name) . '</strong>, and I am writing to express my interest in the
                <strong>' . htmlentities($request->job_title) . '</strong> position at NGO Forum on Cambodia.
            </p>

            <p style="white-space:pre-line;">' . nl2br(htmlentities($request->message)) . '</p>

            <p>Thank you for your time and consideration.</p>

            <p>
                Best regards,<br>
                Name: <strong>' . htmlentities($request->name) . '</strong><br>
                Phone: ' . ($request->phone) . '<br>
                Email: ' . htmlentities($request->email) . '
            </p>
        ';


        Mail::send([], [], function ($message) use ($request, $html) {
            $message->to("mengseu@ngoforum.org.kh")
                ->from($request->email, $request->name) // ✅ add who sent it
                ->subject("Job Application – " . $request->job_title)
                ->html($html);

            // Attach CV if included
            if ($request->hasFile('cv')) {
                $message->attach($request->file('cv')->getRealPath(), [
                    'as'   => $request->file('cv')->getClientOriginalName(),
                    'mime' => $request->file('cv')->getMimeType(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Application sent successfully!'
        ]);
    }
}
