<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('clients')->attempt($credentials)) {
            return redirect()->route('clients.dashboard');
        }

        session()->flash('error', 'Please check your login credentials.');
        return back();
    }

    public function logout()
    {
        Auth::guard('clients')->logout();
        return redirect()->route('clients.login');
    }
}
