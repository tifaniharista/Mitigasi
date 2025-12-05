<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi - Document Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .status-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .status-card {
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
            width: 80px; /* Ukuran logo minimalis */
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
            color: #6c757d; /* Warna ikon netral */
        }
    </style>
</head>
<body>
    <div class="status-container">
        <div class="col-md-6 col-lg-5">
            <div class="card status-card">
                <div class="card-body">

                    <div class="logo-wrapper">
                        <img src="{{ asset('images/logo1.png') }}" alt="Logo Perusahaan" class="logo">
                    </div>

                    <h3 class="text-center mb-3">Menunggu Verifikasi</h3>
                    <div class="text-center mb-4">
                        <span class="badge bg-warning text-dark p-2 fs-6">
                            <i class="fas fa-user-clock me-1"></i>Status: Menunggu Verifikasi
                        </span>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Sedang dalam proses verifikasi</strong><br>
                        Akun Anda sedang ditinjau oleh administrator. Anda akan dapat mengakses sistem setelah akun disetujui.
                    </div>

                    <div class="d-grid gap-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-sign-out-alt me-2"></i>Kembali
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
        setInterval(function() {
            fetch('{{ route("check-verification-status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        window.location.href = '{{ route("dashboard") }}';
                    } else if (data.rejected) {
                        window.location.href = '{{ route("rejection.show") }}';
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 30000);
    </script>
</body>
</html>
