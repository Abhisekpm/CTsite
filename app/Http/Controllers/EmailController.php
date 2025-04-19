<?php

namespace App\Http\Controllers;

use App\Rules\ReCaptcha;
use App\Mail\SendEmail;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
            // 'g-recaptcha-response' => ['required', new ReCaptcha]
        ]);



        Email::create($request->all());

        return redirect()->back()
                         ->with(['success' => 'Thank you for contact us. we will contact you shortly.']);
    }
}
