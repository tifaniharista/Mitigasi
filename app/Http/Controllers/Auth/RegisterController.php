<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Opd;
use App\Models\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * Menampilkan form registrasi
     */
    public function showRegistrationForm()
    {
        $opds = Opd::active()->get();
        $captcha = Captcha::createCaptcha();

        return view('auth.register', compact('opds', 'captcha'));
    }

    /**
     * Memproses registrasi user baru dengan role viewer
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
            'opd_id' => 'required|exists:opds,id',
            'captcha_code' => 'required|string|size:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung minimal 1 huruf kapital, 1 huruf kecil, 1 angka, dan 1 karakter spesial.',
            'opd_id.required' => 'Pilih OPD wajib diisi.',
            'opd_id.exists' => 'OPD yang dipilih tidak valid.',
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi.');
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_VIEWER,
                'opd_id' => $request->opd_id,
                'is_active' => true,
                'status_verifikasi' => User::STATUS_PENDING,
            ]);

            Log::info('User registered as viewer - pending verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'opd_id' => $user->opd_id
            ]);

            Auth::login($user);

            return redirect()->route('waiting-verification')
                ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu verifikasi administrator.');

        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal melakukan registrasi: ' . $e->getMessage())
                ->withInput();
        }
    }
}
