<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/clients/dashboard';

    public function showResetForm(Request $request, $token = null)
    {
        return view('customer.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    protected function broker()
    {
        return Password::broker('clients');
    }

    // protected function guard()
    // {
    //     return \Auth::guard('clients');
    // }

    public function reset(Request $request)
    {
        // Validate input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Attempt to reset the password
        $status = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($client, $password) {
                $client->password = Hash::make($password);
                $client->setRememberToken(Str::random(60));
                $client->save();

                event(new PasswordReset($client));

                $this->guard()->login($client);
            }
        );

        // Redirect accordingly
        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('clients.login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
