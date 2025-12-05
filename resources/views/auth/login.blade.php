<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Document Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(rgba(255, 255, 255, 0.469), rgba(4, 18, 29, 0.921)),
                        url('/images/gresik.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 420px; /* Pertahankan lebar maksimum seperti awal */
        }
        .card-body {
            padding: 2.5rem;
        }
        .logo-wrapper {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo {
            width: 80px;
            height: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #004882;
            box-shadow: 0 0 0 0.2rem rgba(0, 72, 130, 0.25);
        }
        .btn-login {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: #004882;
            border-color: #004882;
            color: white;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 72, 130, 0.4);
            background-color: #003366;
            border-color: #003366;
            color: white;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .form-check-input:checked {
            background-color: #004882;
            border-color: #004882;
        }

        /* Animation untuk card */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .btn-welcome {
            padding: 10px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: transparent;
            border: 1px solid #004882;
            color: #004882;
        }
        .btn-welcome:hover {
            background-color: #004882;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 72, 130, 0.3);
        }
        /* reCAPTCHA Styling */
        .g-recaptcha {
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
        }

        .g-recaptcha > div {
            margin: 0 auto;
        }

        /* Responsive reCAPTCHA */
        @media (max-width: 480px) {
            .g-recaptcha {
                transform: scale(0.85);
                transform-origin: 0 0;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-body">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/logo1.png') }}" alt="Logo Perusahaan" class="logo">
                    <h3 class="h4 fw-normal mt-3 mb-1" style="color: #004882;">Login Akun</h3>
                    <p class="text-muted">Aplikasi Mitigasi</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan email Anda">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password Anda">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                    </div>

                    <!-- CAPTCHA Section -->
                    <div class="form-full-width">
                        <div class="mb-3">
                            <label class="form-label">Verifikasi Kode Keamanan</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if(isset($captcha) && $captcha->image_data)
                                    <img src="{{ $captcha->getImageBase64() }}" alt="CAPTCHA" id="captchaImage"
                                        style="border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                                @else
                                    <div id="captchaImage" style="width: 150px; height: 60px; border: 1px solid #ddd;
                                        display: flex; align-items: center; justify-content: center;
                                        background: #f8f9fa; border-radius: 4px; margin-left: 6px;">
                                        <span class="text-muted">Loading CAPTCHA...</span>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="refreshCaptcha"
                                        style="height: fit-content;" title="Refresh CAPTCHA">
                                    <i class="fas fa-redo-alt"></i>
                                </button>
                            </div>
                            <div class="input-container">
                                <input type="text" class="form-control" id="captcha_code" name="captcha_code"
                                    required placeholder="Masukkan kode di atas" maxlength="6"
                                    style="text-transform: uppercase;">
                                @error('captcha_code')
                                    <div class="text-danger small mt-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted">Belum punya akun?
                            <a href="{{ route('register') }}" class="text-decoration-none" style="color: #004882; font-weight: 500;">Daftar di sini</a>
                        </p>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('welcome') }}" class="btn btn-welcome w-100">
                            <i class="fas fa-home me-2"></i>Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const refreshCaptchaBtn = document.getElementById('refreshCaptcha');
            const captchaImage = document.getElementById('captchaImage');
            const captchaInput = document.getElementById('captcha_code');

            if (refreshCaptchaBtn) {
                refreshCaptchaBtn.addEventListener('click', function() {
                    refreshCaptcha();
                });
            }

            // Auto-uppercase CAPTCHA input
            if (captchaInput) {
                captchaInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }

            function refreshCaptcha() {
                // Show loading
                const originalContent = captchaImage.innerHTML;
                captchaImage.innerHTML = '<span class="text-muted">Loading...</span>';

                fetch('{{ route("captcha.refresh") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update CAPTCHA image
                        captchaImage.innerHTML = `<img src="${data.image_url}" alt="CAPTCHA"
                                                style="border: 1px solid #ddd; border-radius: 4px; padding: 5px;">`;
                        // Clear CAPTCHA input
                        if (captchaInput) {
                            captchaInput.value = '';
                        }
                    } else {
                        throw new Error('Failed to refresh CAPTCHA');
                    }
                })
                .catch(error => {
                    console.error('Error refreshing CAPTCHA:', error);
                    captchaImage.innerHTML = originalContent;
                    alert('Gagal memuat CAPTCHA baru. Silakan refresh halaman.');
                });
            }

            // Initial load CAPTCHA jika belum ada gambar
            if (captchaImage && !captchaImage.querySelector('img')) {
                refreshCaptcha();
            }
        });
    </script>
</body>
</html>
