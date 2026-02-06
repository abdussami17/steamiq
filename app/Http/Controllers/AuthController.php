<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    // Show register page
    public function registerLoginPage()
    {
        return view('auth.login');
    }

    // Handle register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);
    
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
        ]);
    
        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }
    


    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index')
                             ->with('success', 'Logged in successfully!');
        }
    
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }
    
    
    
    
    
    

    // Dashboard
    public function dashboard()
    {
        $user = Auth::user();
        if($user->role != 1){
            return redirect()->route('dashboard.index'); 
        }
        return view('dashboard.index'); 
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}
