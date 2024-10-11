<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function sendEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'number' => 'required',
            'category' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        $data = $request->all();
        // Mengirim email
        Mail::to('test@example.com.com')->send(new ContactMail($data));

        return back()->with('success', '"Email sent successfully!"');
    }
}
