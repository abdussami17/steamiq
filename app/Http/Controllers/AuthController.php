<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


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
    



        public function login(Request $request)
        {
            // ✅ Step 1: Form Validation
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'g-recaptcha-response' => 'required',
            ]);
    
            // ✅ Step 2: Verify CAPTCHA from Google
            $captchaResponse = Http::withoutVerifying()->asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => '6LduRqgsAAAAAGx1OwP1TN86O3IyIao28NQqJ2Ow', // 🔹 Direct secret key
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ]
            );
    
            $captchaResult = $captchaResponse->json();
    
            if (!($captchaResult['success'] ?? false)) {
                return back()
                    ->withErrors(['captcha' => 'Captcha verification failed. Try again.'])
                    ->withInput();
            }
    
            // ✅ Step 3: Normal Login Logic
            if (Auth::attempt($request->only('email', 'password'))) {
                $request->session()->regenerate();
    
                return redirect()->route('dashboard.index')
                                 ->with('success', 'Logged in successfully!');
            }
    
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }
    
    
    
    
    
    
    
    


    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dashboard.index')->with('success', 'Logged out successfully!');
    }
}
