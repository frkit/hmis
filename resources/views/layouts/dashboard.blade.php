<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HMIS</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 260px;
            --topbar-h: 64px;
            --bg:        #0d1117;
            --surface:   #161b22;
            --surface2:  #1c2128;
            --border:    #30363d;
            --text:      #e6edf3;
            --text-muted:#8b949e;
            --accent:    #2ecc71;
            --accent-dk: #27ae60;
            --danger:    #f85149;
            --warning:   #d29922;
            --info:      #58a6ff;
            --purple:    #bc8cff;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: transform 0.35s cubic-bezier(.4,0,.2,1), opacity 0.3s;
        }
        /* Light mode — content area goes white, sidebar stays dark */
        body.light-mode {
            --bg:        #f4f6f9;
            --surface:   #ffffff;
            --surface2:  #f0f2f5;
            --border:    #dde1e7;
            --text:      #1a1f2e;
            --text-muted:#6b7280;
        }
        /* Sidebar always stays dark regardless of theme */
        .sidebar {
            --bg:        #0d1117;
            --surface:   #161b22;
            --surface2:  #1c2128;
            --border:    #30363d;
            --text:      #e6edf3;
            --text-muted:#8b949e;
        }
        body.light-mode .topbar { box-shadow: 0 1px 8px rgba(0,0,0,0.08); }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--border);
            min-height: var(--topbar-h);
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dk));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff;
            box-shadow: 0 4px 12px rgba(46,204,113,0.3);
            flex-shrink: 0;
        }
        .logo-text { font-size: 17px; font-weight: 700; color: var(--text); letter-spacing: -0.3px; }
        .logo-sub  { font-size: 10px; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; margin-top: 1px; }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 12px 10px 6px;
            margin-top: 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; flex-shrink: 0; }
        .nav-item:hover { background: var(--surface2); color: var(--text); }
        .nav-item.active {
            background: rgba(46,204,113,0.12);
            color: var(--accent);
        }
        .nav-item.active i { color: var(--accent); }
        .nav-item .badge {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
        }
        .nav-item .badge.red { background: var(--danger); }
        .nav-item .badge.blue { background: var(--info); }

        /* Submenu */
        .has-sub > .sub-arrow { margin-left: auto; font-size: 11px; transition: transform 0.2s; }
        .has-sub.open > .sub-arrow { transform: rotate(90deg); }
        .sub-menu { display: none; padding-left: 18px; margin-top: 2px; }
        .has-sub.open + .sub-menu { display: block; }
        .sub-menu .nav-item { font-size: 13px; padding: 8px 12px; }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--surface2);
            border-radius: 10px;
        }
        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .user-name  { font-size: 13px; font-weight: 600; color: var(--text); }
        .user-role  { font-size: 11px; color: var(--text-muted); }
        .user-logout {
            margin-left: auto;
            color: var(--text-muted);
            background: none; border: none;
            cursor: pointer; font-size: 14px;
            transition: color 0.2s;
        }
        .user-logout:hover { color: var(--danger); }

        /* ─── TOPBAR ─── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-menu-btn {
            background: none; border: none;
            color: var(--text-muted); font-size: 18px;
            cursor: pointer; display: none;
            padding: 4px 8px;
            transition: color 0.2s;
        }
        .topbar-menu-btn:hover { color: var(--text); }

        .topbar-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .topbar-breadcrumb .current { color: var(--text); font-weight: 600; }
        .topbar-breadcrumb i { font-size: 10px; }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text-muted);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.15s;
            position: relative;
            text-decoration: none;
        }
        .topbar-btn:hover { color: var(--text); background: var(--bg); }
        .topbar-btn .dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 7px; height: 7px;
            background: var(--danger);
            border-radius: 50%;
            border: 1.5px solid var(--surface);
        }
        .theme-toggle {
            width: 64px; height: 32px;
            border-radius: 20px;
            background: var(--surface2);
            border: 1px solid var(--border);
            display: flex; align-items: center;
            cursor: pointer; padding: 3px;
            transition: background 0.3s;
            position: relative;
        }
        .theme-toggle .t-knob {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; color: #fff;
            transition: transform 0.3s;
        }
        body.light-mode .theme-toggle .t-knob { transform: translateX(32px); background: #f0883e; }

        .topbar-time {
            font-size: 12px;
            color: var(--text-muted);
            padding: 0 10px;
            border-left: 1px solid var(--border);
            margin-left: 4px;
        }

        /* ─── PAGE CONTENT ─── */
        .page-content {
            flex: 1;
            padding: 28px;
        }

        /* ─── FOOTER ─── */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .footer-left { font-size: 12px; color: var(--text-muted); }
        .footer-left strong { color: var(--accent); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a {
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s;
        }
        .footer-links a:hover { color: var(--text); }
        .footer-version {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-muted);
        }
        .footer-version .v-badge {
            background: rgba(46,204,113,0.15);
            color: var(--accent);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        /* ─── SIDEBAR OVERLAY (mobile) ─── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 90;
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-wrapper { margin-left: 0; }
            .topbar-menu-btn { display: flex; }
        }

        @yield('extra-styles')
    </style>

    @yield('head')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fa-solid fa-hospital"></i></div>
        <div>
            <div class="logo-text">HMIS</div>
            <div class="logo-sub">Health Management System</div>
        </div>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section-label">Main</div>

        <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>

        <div class="nav-section-label">Patients</div>

        <a href="#" class="nav-item">
            <i class="fa-solid fa-user-injured"></i> Patients
            <span class="badge">New</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-calendar-check"></i> Appointments
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-bed-pulse"></i> In-Patient (IPD)
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-person-walking-arrow-right"></i> Out-Patient (OPD)
        </a>

        <div class="nav-section-label">Clinical</div>

        <a href="{{ route('users.index', ['designation' => 'doctor']) }}" class="nav-item {{ request('designation') == 'doctor' ? 'active' : '' }}">
            <i class="fa-solid fa-user-doctor"></i> Doctors & Staff
        </a>

        <a href="{{ route('doctors.departments') }}" class="nav-item {{ request()->routeIs('doctors.departments') ? 'active' : '' }}">
            <i class="fa-solid fa-hospital-user"></i> Departments
        </a>

        <a href="#" class="nav-item">
            <i class="fa-solid fa-microscope"></i> Laboratory
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-x-ray"></i> Radiology
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-pills"></i> Pharmacy
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-file-medical"></i> Medical Reports
        </a>

        <div class="nav-section-label">Finance</div>

        <a href="#" class="nav-item">
            <i class="fa-solid fa-file-invoice-dollar"></i> Billing
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-chart-bar"></i> Reports
        </a>

        <div class="nav-section-label">Operations</div>

        <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i> Task Management
        </a>
        <a href="{{ route('inventory.index') }}" class="nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked"></i> Inventory
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section-label">Administration</div>

        <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i> User Management
        </a>
        <a href="#" class="nav-item">
            <i class="fa-solid fa-sliders"></i> Settings
        </a>
        @endif

    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="user-logout" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ═══ MAIN WRAPPER ═══ -->
<div class="main-wrapper">

    <!-- ─ TOPBAR ─ -->
    <header class="topbar">
        <button class="topbar-menu-btn" onclick="openSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar-breadcrumb">
            <i class="fa-solid fa-house" style="font-size:12px"></i>
            <i class="fa-solid fa-chevron-right"></i>
            <span class="current">@yield('page-title', 'Dashboard')</span>
        </div>

        <div class="topbar-right">
            <!-- Light/Dark Toggle -->
            <button class="theme-toggle" id="themeToggle" title="Toggle light/dark mode" onclick="toggleTheme()">
                <div class="t-knob" id="themeKnob"><i class="fa-solid fa-moon" id="themeIcon"></i></div>
            </button>
            <button class="topbar-btn" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <button class="topbar-btn" title="Notifications">
                <i class="fa-solid fa-bell"></i>
                <span class="dot"></span>
            </button>
            <button class="topbar-btn" title="Messages">
                <i class="fa-solid fa-envelope"></i>
            </button>
            <div class="topbar-time" id="clockDisplay"></div>
        </div>
    </header>

    <!-- ─ PAGE CONTENT ─ -->
    <main class="page-content">
        @yield('content')
    </main>

    <!-- ─ FOOTER ─ -->
    <footer class="footer">
        <div class="footer-left">
            &copy; {{ date('Y') }} <strong>HMIS</strong> &mdash; Health Management Information System. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Support</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
        </div>
        <div class="footer-version">
            <span>Build</span>
            <span class="v-badge">v1.0.0</span>
        </div>
    </footer>

</div><!-- /main-wrapper -->

<!-- Logout form hidden (handled inline above) -->

<script>
    // Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clockDisplay').textContent =
            now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', hour12:true });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Mobile sidebar
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
    }

    // Theme toggle
    function toggleTheme() {
        const body = document.body;
        const icon = document.getElementById('themeIcon');
        body.classList.toggle('light-mode');
        const isLight = body.classList.contains('light-mode');
        icon.className = isLight ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        localStorage.setItem('hmis-theme', isLight ? 'light' : 'dark');
    }
    // Restore saved theme on load
    (function() {
        if (localStorage.getItem('hmis-theme') === 'light') {
            document.body.classList.add('light-mode');
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = 'fa-solid fa-sun';
        }
    })();
    // Also restore on DOMContentLoaded for show-btn visibility
    document.addEventListener('DOMContentLoaded', function() {
        const showBtns = document.querySelectorAll('.sidebar-show-btn');
        if (localStorage.getItem('hmis-theme') === 'light') {
            showBtns.forEach(b => b.style.display = 'flex');
        }
    });
</script>

@yield('scripts')
</body>
</html>
