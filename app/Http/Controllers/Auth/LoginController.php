<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Attempt to log the user in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        // Check if the user exists
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            session()->flash('error', 'Please check your login credentials.');
            return false;
        }
    
        // Check if user is inactive (2) or blocked (3)
        if ($user->status == 2) {
            session()->flash('error', 'Your account is inactive. Please contact support.');
            return false;
        }
    
        if ($user->status == 3) {
            session()->flash('error', 'Your account has been blocked. Please contact support.');
            return false;
        }
    
        // Attempt to log in the user
        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            session()->flash('error', 'Please check your login credentials.');
            return false;
        }
    
        return true;
    }

}
