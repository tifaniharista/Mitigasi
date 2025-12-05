<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Document Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(rgba(255, 255, 255, 0.469), #04121deb),
                        url('/images/gresik.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            width: 900px;
            max-width: 95%;
        }
        .card-body {
            padding: 2.5rem;
            display: flex;
            gap: 2rem;
        }
        .logo-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem 1rem;
            border-right: 1px solid #e9ecef;
        }
        .form-section {
            flex: 2;
            padding: 1rem 0;
        }
        .logo {
            width: 100px;
            height: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            margin-bottom: 1.5rem;
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
        .btn-register {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: #004882;
            border-color: #004882;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 72, 130, 0.4);
            background-color: #003366;
            border-color: #003366;
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
        .badge {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .welcome-text {
            color: #004882;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        /* Popup Warning Styling */
        .password-warning {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 12px 15px;
            margin-top: 5px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.3s ease-out;
        }
        .password-warning.show {
            display: block;
        }
        .password-warning .warning-content {
            color: #721c24;
            font-size: 0.85rem;
            margin: 0;
        }
        .password-warning .warning-content i {
            color: #dc3545;
            margin-right: 8px;
        }

        .confirm-warning {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 12px 15px;
            margin-top: 5px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.3s ease-out;
        }
        .confirm-warning.show {
            display: block;
        }
        .confirm-warning .warning-content {
            color: #721c24;
            font-size: 0.85rem;
            margin: 0;
        }

        /* Input container dengan posisi relative */
        .input-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        /* Success state */
        .form-control.success {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        /* Error state */
        .form-control.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-full-width {
            grid-column: 1 / -1;
        }

        /* Password toggle button - POSISI DIPERBAIKI */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .password-toggle:hover {
            color: #004882;
        }

        /* Tambahkan padding right pada input untuk memberi ruang ikon */
        .form-control {
            padding-right: 45px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .register-card {
                width: 100%;
                margin: 10px;
            }
            .card-body {
                flex-direction: column;
                padding: 1.5rem;
            }
            .logo-section {
                border-right: none;
                border-bottom: 1px solid #e9ecef;
                padding: 1rem 0 2rem 0;
            }
            .form-section {
                padding: 1rem 0 0 0;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
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

        .register-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0 0 0;
        }
        .feature-list li {
            padding: 0.5rem 0;
            color: #495057;
            font-size: 0.9rem;
        }
        .feature-list li i {
            color: #004882;
            margin-right: 0.5rem;
        }

        .btn-welcome {
            padding: 8px 16px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: transparent;
            border: 1px solid #004882;
            color: #004882;
            font-size: 0.9rem;
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
    <div class="register-container">
        <div class="register-card">
            <div class="card-body">
                <!-- Bagian Logo dan Informasi -->
                <div class="logo-section">
                    <img src="{{ asset('images/logo1.png') }}" alt="Logo Perusahaan" class="logo">
                    <div class="welcome-text">Selamat Datang</div>
                    <p class="subtitle">Aplikasi Mitigasi</p>

                    <div class="mb-3">
                        <span class="badge bg-secondary">
                            <i class="fas fa-eye me-1"></i>Role: Viewer
                        </span>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('welcome') }}" class="btn btn-welcome">
                            <i class="fas fa-home me-1"></i>Kembali ke Beranda
                        </a>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted small">Sudah punya akun?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm"
                        style="color: #005ba5; border-color: #005ba5;"
                        onmouseover="this.style.backgroundColor='#005ba5'; this.style.color='white'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#005ba5'">
                            <i class="fas fa-sign-in-alt me-1"></i>Login di sini
                        </a>
                    </div>
                </div>

                <!-- Bagian Form Registrasi -->
                <div class="form-section">
                    <h3 class="h4 mb-4" style="color: #004882;">
                        <i class="fas fa-user-plus me-2"></i>Buat Akun Baru
                    </h3>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf

                        <div class="form-grid">
                            <div class="form-full-width">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama lengkap Anda">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.com">
                            </div>

                            <div class="mb-3">
                                <label for="opd_id" class="form-label">OPD / Organisasi</label>
                                <select class="form-control" id="opd_id" name="opd_id" required>
                                    <option value="">Pilih OPD</option>
                                    @foreach($opds as $opd)
                                        <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>
                                            {{ $opd->code }} - {{ $opd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Password Input dengan Warning Popup -->
                            <div class="input-container">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password yang kuat">
                                <button type="button" class="password-toggle" id="togglePassword" style="margin-top: 13pt;">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Popup Warning untuk Password -->
                                <div class="password-warning" id="passwordWarning">
                                    <p class="warning-content">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span id="warningText">Password harus mengandung minimal 8 karakter dengan huruf kapital, angka, dan karakter spesial</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Confirm Password Input dengan Warning Popup -->
                            <div class="input-container">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password">
                                <button type="button" class="password-toggle" id="toggleConfirmPassword" style="margin-top: 13pt;">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Popup Warning untuk Confirm Password -->
                                <div class="confirm-warning" id="confirmWarning">
                                    <p class="warning-content">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Password tidak cocok
                                    </p>
                                </div>
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

                        <div class="form-full-width">
                            <button type="submit" class="btn btn-register w-100" style="color: white;" id="submitBtn">
                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submitBtn');
            const passwordWarning = document.getElementById('passwordWarning');
            const confirmWarning = document.getElementById('confirmWarning');
            const warningText = document.getElementById('warningText');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
            const refreshCaptchaBtn = document.getElementById('refreshCaptcha');
            const captchaImage = document.getElementById('captchaImage');

            let isPasswordValid = false;
            let isPasswordMatch = false;

            // Fungsi untuk mengecek kekuatan password
            function checkPasswordStrength(password) {
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[@$!%*?&]/.test(password)
                };

                // Cek jika semua requirements terpenuhi
                const allMet = Object.values(requirements).every(Boolean);

                // Jika password tidak kosong dan ada yang tidak terpenuhi
                if (password.length > 0 && !allMet) {
                    let missingRequirements = [];

                    if (!requirements.length) missingRequirements.push('minimal 8 karakter');
                    if (!requirements.uppercase) missingRequirements.push('huruf kapital (A-Z)');
                    if (!requirements.lowercase) missingRequirements.push('huruf kecil (a-z)');
                    if (!requirements.number) missingRequirements.push('angka (0-9)');
                    if (!requirements.special) missingRequirements.push('karakter spesial (@$!%*?&)');

                    return {
                        isValid: false,
                        message: 'Password harus mengandung: ' + missingRequirements.join(', ')
                    };
                }

                return {
                    isValid: allMet,
                    message: 'Password memenuhi semua persyaratan keamanan'
                };
            }

            // Fungsi untuk menampilkan warning
            function showPasswordWarning(message) {
                warningText.textContent = message;
                passwordWarning.classList.add('show');
                passwordInput.classList.add('error');
                passwordInput.classList.remove('success');
                isPasswordValid = false;
            }

            function hidePasswordWarning() {
                passwordWarning.classList.remove('show');
                passwordInput.classList.remove('error');
                if (passwordInput.value.length > 0) {
                    passwordInput.classList.add('success');
                }
                isPasswordValid = true;
            }

            function showConfirmWarning() {
                confirmWarning.classList.add('show');
                confirmPasswordInput.classList.add('error');
                confirmPasswordInput.classList.remove('success');
                isPasswordMatch = false;
            }

            function hideConfirmWarning() {
                confirmWarning.classList.remove('show');
                confirmPasswordInput.classList.remove('error');
                if (confirmPasswordInput.value.length > 0 && passwordInput.value === confirmPasswordInput.value) {
                    confirmPasswordInput.classList.add('success');
                }
                isPasswordMatch = true;
            }

            // Validasi real-time untuk password
            passwordInput.addEventListener('input', function() {
                const password = this.value;

                if (password.length === 0) {
                    passwordWarning.classList.remove('show');
                    passwordInput.classList.remove('error', 'success');
                    isPasswordValid = false;
                } else {
                    const result = checkPasswordStrength(password);

                    if (!result.isValid) {
                        showPasswordWarning(result.message);
                    } else {
                        hidePasswordWarning();
                    }
                }

                // Juga validasi konfirmasi password
                validatePasswordMatch();
                updateSubmitButton();
            });

            // Validasi real-time untuk konfirmasi password
            confirmPasswordInput.addEventListener('input', function() {
                validatePasswordMatch();
                updateSubmitButton();
            });

            function validatePasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length === 0) {
                    confirmWarning.classList.remove('show');
                    confirmPasswordInput.classList.remove('error', 'success');
                    isPasswordMatch = false;
                    return;
                }

                if (password !== confirmPassword) {
                    showConfirmWarning();
                } else {
                    hideConfirmWarning();
                }
            }

            // Update status tombol submit
            function updateSubmitButton() {
                if (isPasswordValid && isPasswordMatch) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                }
            }

            // Toggle show/hide password
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });

            toggleConfirmPasswordBtn.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });

            // Refresh CAPTCHA
            if (refreshCaptchaBtn) {
                refreshCaptchaBtn.addEventListener('click', function() {
                    refreshCaptcha();
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
                        const captchaInput = document.getElementById('captcha_code');
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

            // Validasi form sebelum submit
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                // Validasi final password
                const passwordResult = checkPasswordStrength(password);
                if (!passwordResult.isValid) {
                    e.preventDefault();
                    showPasswordWarning(passwordResult.message);
                    passwordInput.focus();
                    return false;
                }

                // Validasi final konfirmasi password
                if (password !== confirmPassword) {
                    e.preventDefault();
                    showConfirmWarning();
                    confirmPasswordInput.focus();
                    return false;
                }

                // Jika semua validasi passed, enable tombol dan show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendaftarkan...';
            });

            // Sembunyikan warning ketika klik di luar
            document.addEventListener('click', function(e) {
                if (passwordInput && !passwordInput.contains(e.target) && passwordWarning && !passwordWarning.contains(e.target)) {
                    // Tidak menyembunyikan warning jika ada error
                    if (isPasswordValid || passwordInput.value.length === 0) {
                        passwordWarning.classList.remove('show');
                    }
                }

                if (confirmPasswordInput && !confirmPasswordInput.contains(e.target) && confirmWarning && !confirmWarning.contains(e.target)) {
                    // Tidak menyembunyikan warning jika ada error
                    if (isPasswordMatch || confirmPasswordInput.value.length === 0) {
                        confirmWarning.classList.remove('show');
                    }
                }
            });

            // Auto uppercase untuk CAPTCHA
            const captchaInput = document.getElementById('captcha_code');
            if (captchaInput) {
                captchaInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }

            // Initial state
            updateSubmitButton();
        });
    </script>
</body>
</html>
