<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        return view('customer.auth.passwords.email');
    }

    public function broker()
    {
        return Password::broker('clients');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Validate email
        $request->validate(['email' => 'required|email']);

        // Attempt to send the reset link
        $status = $this->broker()->sendResetLink(
            $request->only('email')
        ); 
        // dd($request->all(), $status, Password::RESET_LINK_SENT);

        // Return back with status
        return $status === Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }
}
