<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Show Register Form/Create Form
    public function create()
    {
        return view('users.register');
    }

    // Create New User
    public function store(Request $request)
    {
        $formfields = $request->validate([
            'name' => ['required','min:3'],
            'email' => ['required','email',Rule::unique('users','email')],
            'password' => ['required','confirmed','min:6']
        ]);

        // Hash Password
        $formfields['password'] = bcrypt($formfields['password']);

        // Create User
        $user = User::create($formfields);

        // Login
        auth()->login($user);

        return redirect('/')->with('message','User created and logged in successfully!');
    }

    // Logout User
    public function logout(Request $request) {
        // Remove the authentication info from user session
        auth()->logout();
        // Invalidate user session
        $request->session()->invalidate();
        // Regenerate csrf token
        $request->session()->regenerateToken();

        return redirect('/')->with('message','You have been logged out!');
        
    }

    // Show login form
    public function login()
    {
        return view('users.login');
    }

    // Authenticate User
    public function authenticate(Request $request) {
        $formfields = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(auth()->attempt($formfields)) {
            $request->session()->regenerate();

            return redirect('/')->with('message','You are now logged in!');       
        }
            
        return back()->withErrors(['email' => 'Invalid Credentials'])->OnlyInput('email');
    }

}
