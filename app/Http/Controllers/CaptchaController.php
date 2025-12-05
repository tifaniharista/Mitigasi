<?php

namespace App\Http\Controllers;

use App\Models\Captcha;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    /**
     * Generate CAPTCHA baru
     */
    public function generate()
    {
        $captcha = Captcha::createCaptcha();

        return response()->json([
            'success' => true,
            'captcha_id' => $captcha->id,
            'image_url' => route('captcha.image', $captcha->id),
            'code' => $captcha->code, // Hanya untuk testing, hapus di production
        ]);
    }

    /**
     * Get CAPTCHA image
     */
    public function image($id)
    {
        $captcha = Captcha::where('id', $id)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        if (!$captcha->image_data) {
            $imageData = $captcha->generateImage();
        } else {
            $imageData = base64_decode($captcha->image_data);
        }

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Pragma', 'no-cache')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Refresh CAPTCHA
     */
    public function refresh()
    {
        // Hapus CAPTCHA lama dari session yang sama
        Captcha::where('session_id', session()->getId())
            ->where('is_used', false)
            ->delete();

        $captcha = Captcha::createCaptcha();

        return response()->json([
            'success' => true,
            'captcha_id' => $captcha->id,
            'image_url' => route('captcha.image', $captcha->id),
        ]);
    }

    /**
     * Validasi CAPTCHA
     */
    public function validateCaptcha(Request $request)
    {
        $request->validate([
            'captcha_code' => 'required|string|size:6',
        ]);

        $isValid = Captcha::validateCaptcha($request->captcha_code);

        return response()->json([
            'success' => $isValid,
            'message' => $isValid ? 'CAPTCHA valid' : 'CAPTCHA tidak valid'
        ]);
    }
}
