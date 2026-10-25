<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Sheetu Food Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #818cf8;
            --accent: #06b6d4;
            --gradient-primary: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --surface-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-subtle: #e2e8f0;
            --sidebar-width: 270px;
            --shadow-subtle: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            --shadow-card: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
            --shadow-hover: 0 20px 30px -10px rgba(79, 70, 229, 0.15);
            --radius-xl: 20px;
            --radius-lg: 14px;
            --radius-md: 10px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--surface-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ========== SIDEBAR ========== */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            z-index: 1050;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.03);
        }

        .sidebar-brand {
            padding: 24px 20px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .sidebar-brand img {
            max-width: 160px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.06));
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 14px;
            flex-grow: 1;
            overflow-y: auto;
            margin: 0;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .sidebar-menu li {
            margin-bottom: 6px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.22s ease;
            position: relative;
        }

        .sidebar-menu a i {
            font-size: 1.15rem;
            margin-right: 14px;
            min-width: 22px;
            text-align: center;
            transition: transform 0.22s ease;
        }

        .sidebar-menu a:hover {
            color: var(--primary);
            background: #f8fafc;
            transform: translateX(4px);
        }

        .sidebar-menu a:hover i {
            transform: scale(1.1);
        }

        .sidebar-menu a.active {
            background: var(--gradient-primary);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.25);
        }

        .sidebar-menu a.active i {
            color: #ffffff;
        }

        .sidebar-footer {
            padding: 16px 18px 20px;
            border-top: 1px solid #f1f5f9;
            background: #ffffff;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px 16px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9rem;
            color: #dc2626;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: var(--gradient-danger);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25);
            transform: translateY(-2px);
        }

        /* ========== MAIN CONTENT AREA ========== */
        .admin-main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
        }

        /* Top Navbar */
        .admin-navbar {
            height: 72px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(8px);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle-btn {
            display: none;
            background: #f1f5f9;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .menu-toggle-btn:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        .page-badge-title {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--text-main);
            margin: 0;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .admin-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 14px 6px 8px;
            border-radius: 30px;
        }

        .admin-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .admin-user-name {
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--text-main);
        }

        /* Container Body */
        .admin-content-body {
            padding: 28px 32px;
            flex-grow: 1;
        }

        /* Sidebar backdrop for mobile */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1040;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .admin-main {
                margin-left: 0;
            }
            .menu-toggle-btn {
                display: flex;
            }
            .admin-navbar {
                padding: 0 16px;
            }
            .admin-content-body {
                padding: 18px 16px;
            }
        }

        /* Payment Priority Popups */
        .payment-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .payment-modal .modal-header.high-priority {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            padding: 24px 28px;
        }
        .payment-modal .modal-header.low-priority {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            padding: 24px 28px;
        }
        .payment-modal .modal-title {
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .payment-modal .modal-body {
            padding: 30px 28px;
            background: #f8f9fa;
        }
        .payment-info-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #e2e8f0;
        }
        .payment-icon-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }
        .payment-icon-wrapper i {
            font-size: 3.5rem;
            color: #dc3545;
            animation: bounce 2s infinite;
        }
        .payment-icon-wrapper.low i {
            color: #d97706;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .priority-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            margin-top: 8px;
        }
        .priority-badge.high {
            background: #dc3545;
            color: #fff;
        }
        .priority-badge.low {
            background: #ffc107;
            color: #000;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}">
                <img src="https://res.cloudinary.com/daizpwrnu/image/upload/v1764217791/sheetu_food_weblogo_cxgmod.png" 
                     alt="Sheetu Food Logo" 
                     onerror="this.src='{{ asset('admin/admin.png') }}'">
            </a>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.distributes.index') }}" class="{{ request()->routeIs('admin.distributes.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Distributes</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.issues.index') }}" class="{{ request()->routeIs('admin.issues.*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Issues</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.gallery.index') }}" class="{{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                    <i class="bi bi-images"></i>
                    <span>Gallery</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.weekfoods.index') }}" class="{{ request()->routeIs('admin.weekfoods.*') ? 'active' : '' }}">
                    <i class="bi bi-egg-fried"></i>
                    <span>Week Foods</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="admin-main">
        <!-- Top Navbar -->
        <header class="admin-navbar">
            <div class="nav-left">
                <button class="menu-toggle-btn" id="menuToggleBtn" title="Toggle Navigation">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-badge-title">@yield('page_title', 'Dashboard')</h1>
            </div>

            <div class="nav-right">
                <div class="admin-user-pill">
                    <div class="admin-avatar">
                        {{ strtoupper(substr(session('admin_name') ?? session('admin_username') ?? 'A', 0, 1)) }}
                    </div>
                    <span class="admin-user-name">
                        {{ session('admin_name') ?? session('admin_username') ?? 'Admin' }}
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="admin-content-body">
            <!-- Flash Message Alerts -->
            @if(session('flash'))
                <div class="alert alert-{{ session('flash')['type'] ?? 'info' }} alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>{{ session('flash')['msg'] ?? '' }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success_msg'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('success_msg') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error_msg'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ session('error_msg') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- High Priority Payment Popup -->
            @if(!empty($showHighPayment))
            <div class="modal fade payment-modal" id="paymentHighModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header high-priority text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle"></i>
                                High Priority Payment Due
                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="payment-icon-wrapper">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="payment-info-box">
                                <p><strong><i class="fas fa-info-circle"></i> Important Notice:</strong></p>
                                <p>A pending payment needs your immediate attention. Please complete it to continue using the our Sheetu Food Software.</p>
                                <span class="priority-badge high">
                                    <i class="fas fa-bolt"></i> Immediate Payment for continue
                                </span>
                            </div>
                            <div class="alert alert-danger mb-0" role="alert">
                                <i class="fas fa-shield-alt"></i> <strong>Note:</strong> This reminder will continue to appear until the payment is completed and verified.
                            </div>
                        </div>
                        <div class="modal-footer bg-white border-top-0 px-4 pb-4">
                            <a href="YOUR_PAYMENT_URL_HERE" target="_blank" rel="noopener noreferrer" class="btn btn-danger w-100 py-2 fw-bold">
                                <i class="fas fa-credit-card me-1"></i> Pay Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Low Priority Payment Popup -->
            @if(!empty($showLowPayment))
            <div class="modal fade payment-modal" id="paymentLowModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header low-priority text-dark">
                            <h5 class="modal-title">
                                <i class="fas fa-info-circle"></i>
                                Payment Reminder
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="payment-icon-wrapper low">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="payment-info-box">
                                <p><strong><i class="fas fa-bell"></i> Friendly Reminder:</strong></p>
                                <p>Your payment is pending. Please complete it soon to avoid your Sheetu Food services being stopped.</p>
                                <span class="priority-badge low">
                                    <i class="fas fa-flag"></i> Due Payment
                                </span>
                            </div>
                            <div class="alert alert-warning mb-0" role="alert">
                                <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Complete this payment to avoid any future service disruptions.
                            </div>
                        </div>
                        <div class="modal-footer bg-white border-top-0 px-4 pb-4 gap-2">
                            <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">Close</button>
                            <a href="YOUR_PAYMENT_URL_HERE" target="_blank" rel="noopener noreferrer" class="btn btn-warning px-4 fw-bold text-dark">
                                <i class="fas fa-credit-card me-1"></i> Pay Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('adminSidebar');
            const toggleBtn = document.getElementById('menuToggleBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                    backdrop.classList.toggle('active');
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', () => {
                    sidebar.classList.remove('open');
                    backdrop.classList.remove('active');
                });
            }

            // Payment Popups initialization
            @if(!empty($showHighPayment))
                const highModal = new bootstrap.Modal(document.getElementById('paymentHighModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                highModal.show();
                document.getElementById('paymentHighModal').addEventListener('hidden.bs.modal', function () {
                    setTimeout(() => highModal.show(), 100);
                });
            @endif

            @if(!empty($showLowPayment))
                const lowModal = new bootstrap.Modal(document.getElementById('paymentLowModal'));
                lowModal.show();
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
