<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $captcha = Captcha::createCaptcha();
        return view('auth.login', compact('captcha'));
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'captcha_code' => 'required|string|size:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'captcha_code.required' => 'Kode CAPTCHA wajib diisi.',
            'captcha_code.size' => 'Kode CAPTCHA harus 6 karakter.',
        ]);

        // Validasi CAPTCHA manual
        if (!$validator->fails()) {
            $isCaptchaValid = Captcha::validateCaptcha($request->captcha_code);
            if (!$isCaptchaValid) {
                $validator->errors()->add('captcha_code', 'Kode CAPTCHA tidak valid atau sudah kedaluwarsa.');
            }
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Terjadi kesalahan validasi.');
        }

        $credentials = array_merge($request->only('email', 'password'), ['is_active' => true]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Password atau email yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
