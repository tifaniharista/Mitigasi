<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Management System - Aplikasi Mitigasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'SF Pro Display', 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .main-header {
            background: rgba(1, 38, 106, 0.8);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(1, 38, 106, 0.8);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            margin-left: -18px;
            border-radius: 10px;
        }

        .nav-link {
            border: 1.3px solid #ffffff;
            border-radius: 8px;
            font-size: 1.0rem;
            padding: 9px 35px !important;
            font-weight: 500;
            color: #ffffff !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: #ffffff;
            color: #004882 !important;
        }

        .btn-outline-dark {
            font-size: 1.0rem;
            padding: 10px 30px !important;
            background: #003159;
            color: rgb(255, 255, 255);
            border: 1.3px solid #ffffff;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-dark:hover {
            background-color: #ffffff;
            color: #004882 !important;
        }

        /* Hero Section */
        .welcome-page {
            min-height: 85vh;
            background: linear-gradient(#000000, #0000003a),
                        url('https://www.hashmicro.com/id/blog/wp-content/uploads/2022/11/konstruksi-bangunan1-scaled.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
        }

        .hero-section {
            padding: 120px 0 100px;
            color: white;
            text-align: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .btn-hero {
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 10px 15px;
            border: 1.5px solid white;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-hero:hover {
            background-color: white;
            color: #004882;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        /* Features Section */
        .features-section {
            background: white;
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            color: #004882;
            font-weight: 700;
            font-size: 2.5rem;
        }

        .feature-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #004882, #0078D7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.7rem;
        }

        .feature-icon i {
            font-size: 1.8rem;
            color: white;
        }

        .feature-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #004882;
            font-size: 1.3rem;
        }

        /* Footer */
        .main-footer {
            background: #00182c;
            color: white;
            padding: 5px 0 10px;
        }

        .copyright {
            background: #00182c;
            padding-top: 10px;
            margin-top: 20px;
            text-align: center;
            color: #ffffff;
        }

        /* Animations */
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

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .nav-link {
                padding: 8px 20px !important;
                font-size: 0.9rem;
            }

            .btn-outline-dark {
                padding: 8px 20px !important;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg navbar-light py-2">
            <div class="container">
                <a class="navbar-brand" href="{{ route('welcome') }}" style="color: #ffffff !important; display: flex; align-items: center; text-decoration: none;">
                    <img src="{{ asset('images/logo1.png') }}" alt="Logo Mitigasi" style=" width: 55px; height: 55px; margin-right: 15px; margin-left: -18px; border-radius: 10px;">
                    <div>
                        <div style="font-weight: 700; font-size: 1.3rem; line-height: 1.5;">Mitigasi</div>
                        <div style="font-weight: 350; font-size: 0.9rem; line-height: 1.3;">Kabupaten Gresik</div>
                    </div>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <script>
                                window.location.href = "{{ route('dashboard') }}";
                            </script>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                            </li>
                            <li class="nav-item" style="margin-left: 18px;">
                                <a class="nav-link btn btn-outline-dark" href="{{ route('register') }}">Registrasi</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="welcome-page">
        <div class="container">
            <div class="hero-section animate-fade-in">
                <h1 class="hero-title">Selamat Datang di Aplikasi Mitigasi</h1>
                <p class="hero-subtitle">
                    Sistem Manajemen Dokumen Terpadu untuk Monitoring dan Pengawasan Proyek Pembangunan Kabupaten Gresik
                </p>

                @auth
                    <script>
                        window.location.href = "{{ route('dashboard') }}";
                    </script>
                @else
                    <div class="hero-button">
                        <a href="https://drive.google.com/file/d/1KLtTsMnVQJNq51E1Q4j1BNri2HVAVMEZ/view?usp=drive_link" class="btn btn-hero" target="_blank">
                            <i class="fas fa-book me-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title" style="margin-top: -30pt;">Fitur Unggulan Sistem</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h4 class="feature-title">Manajemen Proyek</h4>
                        <p class="text-muted">
                            Kelola seluruh proyek instansi dengan timeline yang terstruktur dan monitoring real-time progress pembangunan.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h4 class="feature-title">Dokumen Digital</h4>
                        <p class="text-muted">
                            Simpan, kelola, dan akses dokumen proyek secara digital dengan sistem arsip yang terorganisir dan aman.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4 class="feature-title">Keamanan</h4>
                        <p class="text-muted">
                            Sistem keamanan dengan kontrol akses berdasarkan peran dan wewenang pengguna yang terintegrasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="container">
            <div class="copyright">
                <p>&copy; 2025 Document Management System - Aplikasi Mitigasi Dinas Komunikasi dan Informatika Kabupaten Gresik. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
