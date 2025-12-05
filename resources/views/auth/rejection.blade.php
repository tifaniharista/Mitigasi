<!DOCTYPE html>
<!-- [file name]: rejection.blade.php -->
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Ditolak - Document Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .rejection-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .rejection-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: white;
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
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .info-item i {
            width: 20px;
            margin-right: 10px;
            color: #6c757d;
        }
        .rejection-reason {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="rejection-container">
        <div class="col-md-6 col-lg-5">
            <div class="card rejection-card">
                <div class="card-body">

                    <div class="logo-wrapper">
                        <img src="{{ asset('images/logo1.png') }}" alt="Logo Perusahaan" class="logo">
                    </div>

                    <h3 class="text-center mb-3 text-danger">Registrasi Ditolak</h3>
                    <div class="text-center mb-4">
                        <span class="badge bg-danger p-2 fs-6">
                            <i class="fas fa-times-circle me-1"></i>Status: Ditolak
                        </span>
                    </div>

                    <div class="bg-light p-3 rounded mb-4">
                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Akun</h6>
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <div>
                                <strong>Nama:</strong> {{ Auth::user()->name }}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email:</strong> {{ Auth::user()->email }}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-building"></i>
                            <div>
                                <strong>OPD:</strong> {{ Auth::user()->opd_name }}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <strong>Tanggal Daftar:</strong> {{ Auth::user()->created_at->translatedFormat('d F Y H:i') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-times"></i>
                            <div>
                                <strong>Tanggal Penolakan:</strong> {{ Auth::user()->rejected_at_formatted }}
                            </div>
                        </div>
                    </div>

                    <div class="rejection-reason">
                        <h6 class="mb-2"><i class="fas fa-comment-exclamation me-2"></i>Alasan Penolakan</h6>
                        <p class="mb-0">{{ Auth::user()->rejection_reason_formatted }}</p>
                    </div>

                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Akun Anda tidak dapat mengakses sistem</strong><br>
                        Registrasi Anda telah ditolak oleh administrator. Untuk informasi lebih lanjut, silakan hubungi administrator sistem.
                    </div>

                    <div class="d-grid gap-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-sign-out-alt me-2"></i>Keluar
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 text-center text-muted small">
                        <i class="fas fa-phone me-1"></i>
                        Butuh bantuan? Hubungi administrator sistem.
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto refresh untuk cek status (jika admin mengubah keputusan)
        setInterval(function() {
            fetch('{{ route("check-verification-status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        // Jika sudah disetujui, redirect ke dashboard
                        window.location.href = '{{ route("dashboard") }}';
                    } else if (!data.rejected) {
                        // Jika status berubah dari rejected ke pending
                        window.location.href = '{{ route("waiting-verification") }}';
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 30000); // 30 detik
    </script>
</body>
</html>
