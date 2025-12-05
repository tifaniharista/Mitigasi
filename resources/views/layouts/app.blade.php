<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .custom-body {
            background-color: #f4f7f9;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .custom-body.loaded {
            opacity: 1;
        }

        .custom-navbar-brand {
            font-weight: bold;
        }

        .custom-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .navbar-logo {
            height: 50px;
            width: 50px;
            margin-right: 8px;
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .navbar-logo:hover {
            transform: scale(1.05);
        }

        .navbar-blue .nav-link {
            color: rgba(255, 255, 255, 0.9);
            transition: color 0.3s ease;
        }

        .navbar-blue .nav-link:hover {
            color: #ffffff;
        }

        .custom-sidebar {
            background-color: #003159;
            border-right: 1px solid #e0e0e0;
            min-height: calc(100vh - 56px);
            padding: 0;
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.05);
            width: 280px;
            transition: all 0.3s ease;
            position: relative;
        }

        .custom-sidebar.collapsed {
            width: 70px;
        }

        .custom-sidebar.collapsed .nav-link span,
        .custom-sidebar.collapsed h6 {
            display: none;
        }

        .custom-sidebar.collapsed .nav-link {
            text-align: center;
            padding: 0.75rem 0.5rem;
            margin: 0 0.5rem;
        }

        .custom-sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .custom-sidebar .nav-link {
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            border-radius: 4px;
            margin: 0 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .custom-sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .custom-sidebar .nav-link:hover::before {
            left: 100%;
        }

        .custom-sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: rgba(255, 255, 255, 0.5);
            transform: translateX(5px);
        }

        .custom-sidebar .nav-link.active {
            color: #003159;
            background-color: #ffffff;
            border-left-color: #ffffff;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(255, 255, 255, 0.2);
            transform: translateX(0);
        }

        .custom-sidebar .nav-link i {
            width: 20px;
            margin-right: 12px;
            transition: margin-right 0.3s ease;
        }

        .custom-sidebar h6 {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
            padding: 0 1.5rem;
            transition: opacity 0.3s ease;
        }

        .custom-alert {
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .custom-dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .dropdown-item {
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #003159;
            color: white;
            transform: translateX(5px);
        }

        .custom-badge {
            font-size: 0.7rem;
            padding: 0.25em 0.4em;
            transition: all 0.3s ease;
        }

        .brand-text-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-left: 8px;
            transition: all 0.3s ease;
        }

        .app-name {
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.2;
            color: white;
        }

        .region-name {
            font-size: 0.85rem;
            font-weight: 400;
            line-height: 1.2;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 2px;
        }

        /* Toggle sidebar button */
        .sidebar-toggle {
            position: absolute;
            top: 10px;
            right: -15px;
            background: #003159;
            border: 2px solid #fff;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .sidebar-toggle:hover {
            background: #004882;
            transform: scale(1.1);
        }

        /* Main content transition */
        .main-content {
            transition: margin-left 0.3s ease;
        }

        /* Loading animation */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        /* Fade in animation for content */
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* NOTIFICATION SYSTEM STYLES */
        .notification-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .notification {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 10px;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .notification.hide {
            transform: translateX(400px);
            opacity: 0;
        }

        .notification-success {
            border-left: 4px solid #28a745;
        }

        .notification-error {
            border-left: 4px solid #dc3545;
        }

        .notification-warning {
            border-left: 4px solid #ffc107;
        }

        .notification-info {
            border-left: 4px solid #17a2b8;
        }

        .notification-progress {
            height: 3px;
            background: #007bff;
            width: 100%;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 4s linear;
        }

        .notification-progress.running {
            transform: scaleX(1);
        }

        /* Scroll to top button */
        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #003159;
            color: white;
            border: none;
            cursor: pointer;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scroll-to-top.show {
            opacity: 1;
            transform: scale(1);
        }

        .scroll-to-top.hide {
            opacity: 0;
            transform: scale(0.8);
        }
    </style>

    <!-- Stack untuk styles tambahan dari child views -->
    @stack('styles')
</head>
<body class="custom-body">
    <!-- Loading Spinner -->
    <div class="loading-spinner">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top hide" id="scrollToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #003159;">
        <div class="container-fluid">
            <a class="navbar-brand custom-navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}" style="margin-top: 0.50rem; margin-left: 0.40rem;">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo Instansi Gresik" class="navbar-logo">
                <div class="brand-text-container" style="margin-top: 0.20rem;">
                    <span class="app-name">Aplikasi Mitigasi</span>
                    <span class="region-name">Kabupaten Gresik</span>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    @auth
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu custom-dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-1"></i>Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <div class="col-xxl-2 col-xl-3 d-none d-xl-block custom-sidebar" id="sidebar">
                <!-- Toggle Button -->
                <div class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-chevron-left" id="toggleIcon"></i>
                </div>

                <div class="pt-4">
                    <div class="px-3 mb-4">
                        <h6 class="text-uppercase small" style="margin-left: 20pt;">Main Navigation</h6>
                    </div>
                    <ul class="nav flex-column pb-4">
                        @foreach(auth()->user()->menu_items as $menu)
                            <li class="nav-item">
                                <a class="nav-link {{ $menu['active'] ? 'active' : '' }}"
                                   href="{{ route($menu['route']) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="{{ $menu['name'] }}">
                                    <i class="{{ $menu['icon'] }}"></i>
                                    <span>{{ $menu['name'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endauth

            <div class="col main-content" id="mainContent">
                <div class="container mt-4 fade-in">
                    <!-- HAPUS ALERT LAMA DI SINI -->
                    <!-- Main Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Notification System -->
    <script>
        class NotificationSystem {
            constructor() {
                this.container = document.getElementById('notificationContainer');
                this.notifications = new Set();
                this.processSessionMessages();
            }

            processSessionMessages() {
                // Process success message - HANYA SATU KALI
                @if(session('success'))
                this.show('{{ session("success") }}', 'success');
                @php session()->forget('success'); @endphp
                @endif

                // Process error message - HANYA SATU KALI
                @if(session('error'))
                this.show('{{ session("error") }}', 'error');
                @php session()->forget('error'); @endphp
                @endif
            }

            show(message, type = 'success', duration = 5000) {
                // Cek jika ada notifikasi dengan pesan yang sama, hapus yang lama
                this.notifications.forEach(id => {
                    const existingNotification = document.getElementById(id);
                    if (existingNotification && existingNotification.textContent.includes(message)) {
                        this.hide(id);
                    }
                });

                const id = 'notification-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;
                notification.id = id;

                notification.innerHTML = `
                    <div class="d-flex align-items-center p-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <i class="fas ${this.getIcon(type)} me-2 text-${this.getColor(type)}"></i>
                                <span class="fw-semibold">${message}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-2" onclick="notificationSystem.hide('${id}')"></button>
                    </div>
                    <div class="notification-progress"></div>
                `;

                this.container.appendChild(notification);
                this.notifications.add(id);

                // Animate in
                setTimeout(() => {
                    notification.classList.add('show');
                    // Start progress bar
                    setTimeout(() => {
                        const progressBar = notification.querySelector('.notification-progress');
                        progressBar.classList.add('running');
                    }, 100);
                }, 100);

                // Auto hide
                if (duration > 0) {
                    setTimeout(() => {
                        this.hide(id);
                    }, duration);
                }

                return id;
            }

            hide(id) {
                const notification = document.getElementById(id);
                if (notification) {
                    notification.classList.remove('show');
                    notification.classList.add('hide');

                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                        this.notifications.delete(id);
                    }, 300);
                }
            }

            hideAll() {
                this.notifications.forEach(id => this.hide(id));
            }

            getIcon(type) {
                const icons = {
                    'success': 'fa-check-circle',
                    'error': 'fa-exclamation-circle',
                    'warning': 'fa-exclamation-triangle',
                    'info': 'fa-info-circle'
                };
                return icons[type] || 'fa-info-circle';
            }

            getColor(type) {
                const colors = {
                    'success': 'success',
                    'error': 'danger',
                    'warning': 'warning',
                    'info': 'info'
                };
                return colors[type] || 'primary';
            }
        }

        // Initialize notification system
        const notificationSystem = new NotificationSystem();

        // Global function untuk show notification dari mana saja
        window.showNotification = function(message, type = 'success', duration = 5000) {
            return notificationSystem.show(message, type, duration);
        };
    </script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show page with fade in effect
            document.body.classList.add('loaded');

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Sidebar toggle functionality
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const toggleIcon = document.getElementById('toggleIcon');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');

                    if (sidebar.classList.contains('collapsed')) {
                        toggleIcon.classList.remove('fa-chevron-left');
                        toggleIcon.classList.add('fa-chevron-right');
                    } else {
                        toggleIcon.classList.remove('fa-chevron-right');
                        toggleIcon.classList.add('fa-chevron-left');
                    }
                });
            }

            // Smooth loading for all links
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't prevent default for external links or links with targets
                    if (this.target === '_blank' || this.hostname !== window.location.hostname) {
                        return;
                    }

                    const href = this.getAttribute('href');
                    if (href && href.startsWith('/') && !href.includes('#')) {
                        e.preventDefault();

                        // Show loading spinner
                        const spinner = document.querySelector('.loading-spinner');
                        spinner.style.display = 'block';

                        // Add fade out effect to content
                        const content = document.querySelector('.fade-in');
                        if (content) {
                            content.style.opacity = '0';
                            content.style.transition = 'opacity 0.3s ease';
                        }

                        setTimeout(() => {
                            window.location.href = href;
                        }, 300);
                    }
                });
            });

            // Form submission handling
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const spinner = document.querySelector('.loading-spinner');
                    if (spinner) {
                        spinner.style.display = 'block';
                    }
                });
            });

            // Add hover effects to cards
            document.querySelectorAll('.custom-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl + / to toggle sidebar
                if (e.ctrlKey && e.key === '/') {
                    e.preventDefault();
                    if (sidebarToggle) {
                        sidebarToggle.click();
                    }
                }

                // Escape key to close modals and dropdowns
                if (e.key === 'Escape') {
                    const openModals = document.querySelectorAll('.modal.show');
                    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');

                    openModals.forEach(modal => {
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    });

                    openDropdowns.forEach(dropdown => {
                        const dropdownInstance = bootstrap.Dropdown.getInstance(dropdown.previousElementSibling);
                        if (dropdownInstance) {
                            dropdownInstance.hide();
                        }
                    });

                    // Juga hide semua notifikasi saat Escape ditekan
                    notificationSystem.hideAll();
                }
            });

            // Scroll to top button functionality
            const scrollToTopBtn = document.getElementById('scrollToTop');

            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.remove('hide');
                    scrollToTopBtn.classList.add('show');
                } else {
                    scrollToTopBtn.classList.remove('show');
                    scrollToTopBtn.classList.add('hide');
                }
            });

            // Prevent logout form double submission
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Logging out...';
                    }
                });
            }

            // Auto-hide notifications when clicking anywhere on page
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.notification') && !e.target.closest('.notification-container')) {
                    // Optional: Uncomment jika ingin hide semua notifikasi saat klik di luar
                    // notificationSystem.hideAll();
                }
            });
        });

        // Handle page before unload
        window.addEventListener('beforeunload', function() {
            document.body.classList.remove('loaded');
            document.body.style.opacity = '0';
        });

        // AJAX error handler global
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
            showNotification('Terjadi kesalahan sistem', 'error');
        });
    </script>

    <!-- Stack untuk scripts tambahan dari child views -->
    @stack('scripts')
</body>
</html>
