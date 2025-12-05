<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Captcha extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'image_data',
        'session_id',
        'is_used',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    /**
     * Generate CAPTCHA code unik
     */
    public static function generateCode($length = 6)
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Hilangkan karakter yang membingungkan
        $charactersLength = strlen($characters);
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }

        return $code;
    }

    /**
     * Buat CAPTCHA baru
     */
    public static function createCaptcha($sessionId = null)
    {
        // Bersihkan CAPTCHA yang sudah kedaluwarsa
        self::cleanExpired();

        $code = self::generateCode();

        // Pastikan kode unik
        while (self::where('code', $code)->exists()) {
            $code = self::generateCode();
        }

        return self::create([
            'code' => $code,
            'session_id' => $sessionId ?? session()->getId(),
            'expires_at' => now()->addMinutes(15), // CAPTCHA berlaku 15 menit
        ]);
    }

    /**
     * Validasi CAPTCHA
     */
    public static function validateCaptcha($code, $sessionId = null)
    {
        $captcha = self::where('code', strtoupper($code))
            ->where('session_id', $sessionId ?? session()->getId())
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($captcha) {
            $captcha->update(['is_used' => true]);
            return true;
        }

        return false;
    }

    /**
     * Hapus CAPTCHA yang sudah kedaluwarsa
     */
    public static function cleanExpired()
    {
        return self::where('expires_at', '<', now())->delete();
    }

    /**
     * Generate gambar CAPTCHA (opsional)
     */
    public function generateImage($width = 150, $height = 50)
    {
        $image = imagecreate($width, $height);

        // Background color
        $bgColor = imagecolorallocate($image, 255, 255, 255);

        // Text color
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // Noise colors
        $noiseColor = imagecolorallocate($image, 100, 100, 100);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Add noise
        for ($i = 0; $i < 50; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
        }

        // Add lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $noiseColor);
        }

        // Calculate text position
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($this->code);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;

        // Add text
        imagestring($image, $fontSize, $x, $y, $this->code, $textColor);

        // Start output buffering
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        $this->update(['image_data' => base64_encode($imageData)]);

        return $imageData;
    }

    /**
     * Get CAPTCHA image as base64
     */
    public function getImageBase64()
    {
        if (!$this->image_data) {
            $this->generateImage();
        }

        return 'data:image/png;base64,' . $this->image_data;
    }

    /**
     * Scope untuk CAPTCHA yang masih aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', now());
    }
}
