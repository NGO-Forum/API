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
        $html = '
            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif;">
            <tr>
                <td align="center">
                <table width="600" cellpadding="20" cellspacing="0" style="background: #ffffff; border-radius: 8px; border:1px solid #e5e5e5;">
                    <tr>
                    <td>

                        <h2 style="color:#1b8b4c; margin-top:0;">New Contact Message</h2>

                        <p style="font-size:15px; color:#333;">
                        Dear NGO Forum Team,
                        </p>

                        <p style="font-size:15px; color:#333;">
                        You have received a new message from the website contact form.
                        </p>

                        <h3 style="font-size:16px; color:#1b8b4c; margin-bottom:5px;">Sender Information</h3>

                        <p style="font-size:15px; color:#333; line-height:1.5;">
                        <strong>Name:</strong> ' . htmlentities($request->first_name . " " . $request->last_name) . '<br>
                        <strong>Email:</strong> ' . htmlentities($request->email) . '<br>
                        <strong>Phone:</strong> ' . htmlentities($request->phone) . '
                        </p>

                        <h3 style="font-size:16px; color:#1b8b4c; margin-bottom:5px;">Message</h3>
                        <p style="font-size:15px; color:#333; line-height:1.5; white-space:pre-line;">
                        ' . nl2br(htmlentities($request->message)) . '
                        </p>

                        <br>

                        <p style="font-size:14px; color:#777;">
                        This message was sent from the NGO Forum on Cambodia website.
                        </p>

                    </td>
                    </tr>
                </table>
                </td>
            </tr>
            </table>
            ';


        Mail::send([], [], function ($message) use ($request, $name, $html) {
            $message->to("info@ngoforum.org.kh")

                // User is the sender (looks natural)
                ->from("no-reply@ngoforum.org.kh", "NGO Forum Website")

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
