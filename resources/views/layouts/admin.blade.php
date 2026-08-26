<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration — DoyaImmo')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --rust: #B5502A;
            --rust-soft: rgba(181, 80, 42, 0.12);
            --ink: #1A1D26;
            --surface: #FFFFFF;
            --border: #E8ECF0;
            --text-soft: #4A5260;
            --muted: #8A91A0;
            --gold: #D4AF37;
            --gold-soft: rgba(184, 150, 40, 0.12);
            --teal: #0E7A7A;
            --teal-soft: rgba(14, 122, 122, 0.12);
            --green: #1E7A47;
            --green-soft: rgba(30, 122, 71, 0.12);
            --red: #C62828;
            --red-soft: rgba(198, 40, 40, 0.12);
            --radius: 16px;
            --display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #F7F9FC;
            color: var(--ink);
            line-height: 1.6;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            width: 260px;
            background: var(--ink);
            color: #fff;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar .brand {
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar .brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--rust);
            color: #fff;
            font-family: var(--display);
            font-weight: 800;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar .brand-name {
            font-family: var(--display);
            font-weight: 700;
            font-size: 18px;
            color: #fff;
        }

        .sidebar .brand-name span {
            color: var(--gold);
        }

        .nav-group-label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6A7280;
            margin: 16px 0 8px;
        }

        .navlist {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .navlink {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #B9BFCC;
            text-decoration: none;
            font-size: 13.5px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .navlink:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .navlink.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .navlink .ic {
            width: 20px;
            text-align: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .navlink .badge {
            margin-left: auto;
            background: var(--rust);
            color: #fff;
            font-size: 11px;
            padding: 1px 10px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        .navlink .badge.red {
            background: var(--red);
        }

        .sidebar-foot {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .agency-mini {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
        }

        .agency-mini .av {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: var(--ink);
            flex-shrink: 0;
        }

        .agency-mini .name {
            font-weight: 600;
            font-size: 13px;
            color: #fff;
        }

        .agency-mini .role {
            font-size: 11.5px;
            color: #8A91A0;
        }

        /* ==================== MAIN ==================== */
        .main {
            margin-left: 260px;
            flex: 1;
            padding: 24px 32px 40px;
            min-height: 100vh;
            width: calc(100% - 260px);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .burger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
        }

        .burger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--ink);
            margin: 4px 0;
            transition: 0.2s;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title {
            font-family: var(--display);
            font-weight: 700;
            font-size: 20px;
        }

        .page-sub {
            font-size: 13px;
            color: var(--muted);
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .topbar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* ==================== HOME BUTTON ==================== */
        .home-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid var(--border);
            background: #fff;
        }

        .home-btn:hover {
            border-color: var(--rust);
            color: var(--rust);
            background: var(--rust-soft);
        }

        /* ==================== CONTENT ==================== */
        .content {
            max-width: 1200px;
            width: 100%;
        }

        .view {
            display: none;
        }

        .view.active {
            display: block;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .section-head h2 {
            font-family: var(--display);
            font-weight: 700;
            font-size: 22px;
        }

        .section-head p {
            color: var(--muted);
            font-size: 14px;
        }

        /* ==================== KPI GRID ==================== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .kpi-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 24px;
        }

        .kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .kpi-ic {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .kpi-trend {
            font-size: 11.5px;
            font-weight: 600;
        }

        .kpi-trend.trend-up {
            color: var(--green);
        }

        .kpi-trend.trend-down {
            color: var(--red);
        }

        .kpi-value {
            font-family: var(--display);
            font-weight: 700;
            font-size: 28px;
        }

        .kpi-label {
            font-size: 13px;
            color: var(--muted);
        }

        /* ==================== GRID ==================== */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 820px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 24px;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .panel-head h3 {
            font-family: var(--display);
            font-size: 15px;
        }

        .see-all {
            font-size: 12.5px;
            color: var(--rust);
            text-decoration: none;
            font-weight: 600;
        }

        /* ==================== TABLE ==================== */
        .table-wrap {
            overflow-x: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .table-wrap table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 600px;
        }

        .table-wrap th {
            padding: 14px 18px;
            text-align: left;
            background: #FAFBFC;
            font-weight: 600;
            color: var(--text-soft);
            border-bottom: 1px solid var(--border);
        }

        .table-wrap td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
        }

        .table-wrap tbody tr:hover {
            background: #FAFBFC;
        }

        .cell-main {
            font-weight: 600;
            font-size: 13px;
        }

        .cell-sub {
            font-size: 12px;
            color: var(--muted);
        }

        /* ==================== STATUS PILLS ==================== */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-en_attente,
        .status-attente {
            background: #FFF8E1;
            color: #E65100;
        }

        .status-acceptee,
        .status-active,
        .status-valide {
            background: #E8F5E9;
            color: #1E7A47;
        }

        .status-refusee,
        .status-suspendue,
        .status-inactif {
            background: #FFEBEE;
            color: #C62828;
        }

        .status-confirme {
            background: #E3F2FD;
            color: #0D47A1;
        }

        .status-termine {
            background: #E8F5E9;
            color: #1E7A47;
        }

        .status-annule {
            background: #FFEBEE;
            color: #C62828;
        }

        /* ==================== BESOIN CARD ==================== */
        .besoin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .besoin-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .besoin-card:hover {
            transform: translateY(-2px);
        }

        .tier-bar {
            height: 4px;
        }

        .tier-prem {
            background: #F5A623;
        }

        .tier-std {
            background: #4A90D9;
        }

        .tier-eco {
            background: #7ED321;
        }

        .besoin-body {
            padding: 14px 16px;
        }

        .besoin-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .besoin-type {
            font-family: var(--display);
            font-weight: 700;
            font-size: 14px;
        }

        .besoin-budget {
            font-weight: 700;
            color: var(--rust);
            font-size: 13px;
        }

        .besoin-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--border);
            padding: 2px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            color: var(--text-soft);
        }

        .besoin-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }

        .posted {
            font-size: 12px;
            color: var(--muted);
        }

        /* ==================== AVIS ==================== */
        .avis-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .avis-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
        }

        .avis-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .avis-seal {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--rust-soft);
            color: var(--rust);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        .avis-name {
            font-weight: 600;
            font-size: 14px;
        }

        .avis-date {
            font-size: 12px;
            color: var(--muted);
        }

        .stars {
            margin-left: auto;
            color: #F5A623;
            font-size: 14px;
        }

        .avis-text {
            font-size: 13.5px;
            color: var(--text-soft);
            margin-bottom: 8px;
        }

        .avis-property {
            font-size: 12px;
            color: var(--muted);
        }

        /* ==================== BOUTONS ==================== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-rust {
            background: var(--rust);
            color: #fff;
            border-color: var(--rust);
        }

        .btn-rust:hover {
            background: #9A4523;
            border-color: #9A4523;
            color: #fff;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-soft);
            border-color: var(--border);
        }

        .btn-ghost:hover {
            background: var(--border);
            color: var(--ink);
        }

        .btn-success {
            background: var(--green);
            color: #fff;
            border-color: var(--green);
        }

        .btn-success:hover {
            background: #156B3C;
            border-color: #156B3C;
            color: #fff;
        }

        .btn-danger {
            background: var(--red);
            color: #fff;
            border-color: var(--red);
        }

        .btn-danger:hover {
            background: #B71C1C;
            border-color: #B71C1C;
            color: #fff;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 11.5px;
        }

        .btn-block {
            width: 100%;
            justify-content: center;
        }

        /* ==================== OVERLAY ==================== */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 99;
        }

        .overlay.open {
            display: block;
        }

        /* ==================== FLASH MESSAGES ==================== */
        .flash-message {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .flash-success {
            background: #E8F5E9;
            color: #1E7A47;
            border: 1px solid #C8E6C9;
        }

        .flash-error {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #FFCDD2;
        }

        .flash-info {
            background: #E3F2FD;
            color: #0D47A1;
            border: 1px solid #BBDEFB;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 820px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
                padding: 16px 20px;
                width: 100%;
            }

            .burger {
                display: block;
            }

            .topbar {
                flex-wrap: wrap;
            }

            .kpi-grid {
                grid-template-columns: 1fr 1fr;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }

            .table-wrap table {
                min-width: 500px;
            }

            .besoin-grid {
                grid-template-columns: 1fr;
            }

            .section-head {
                flex-direction: column;
                align-items: stretch;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .top-actions {
                justify-content: flex-start;
            }

            .sidebar {
                width: 260px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span>Immo</span></div>
            </a>

            <div class="nav-group-label">Administration</div>
            <div class="navlist">
                <a class="navlink {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="ic fa-solid fa-gauge"></i> Vue d'ensemble
                </a>
                <a class="navlink {{ request()->routeIs('admin.agences.*') ? 'active' : '' }}"
                    href="{{ route('admin.agences.index') }}">
                    <i class="ic fa-solid fa-building"></i> Agences
                    <span class="badge red">{{ $agencesEnAttente ?? 0 }}</span>
                </a>
                <a class="navlink {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}"
                    href="{{ route('admin.utilisateurs.index') }}">
                    <i class="ic fa-solid fa-user"></i> Clients
                </a>
                <a class="navlink {{ request()->routeIs('admin.biens.*') ? 'active' : '' }}"
                    href="{{ route('admin.biens.index') }}">
                    <i class="ic fa-solid fa-house"></i> Biens
                </a>

                <a class="navlink {{ request()->routeIs('admin.biens.vedette') ? 'active' : '' }}"
                    href="{{ route('admin.biens.vedette') }}">
                    <i class="ic fa-solid fa-star" style="color:#F5A623;"></i> À la une
                    <span class="badge red">{{ \App\Models\BienImmobilier::vedette()->count() }}</span>
                </a>

                <a class="navlink {{ request()->routeIs('admin.demandes.*') ? 'active' : '' }}"
                    href="{{ route('admin.demandes.index') }}">
                    <i class="ic fa-solid fa-house-circle-check"></i> Demandes
                </a>
                <a class="navlink {{ request()->routeIs('admin.propositions.*') ? 'active' : '' }}"
                    href="{{ route('admin.propositions.index') }}">
                    <i class="ic fa-solid fa-handshake"></i> Offres
                </a>
                <a class="navlink {{ request()->routeIs('admin.rendezvous.*') ? 'active' : '' }}"
                    href="{{ route('admin.rendezvous.index') }}">
                    <i class="ic fa-solid fa-calendar-check"></i> Rendez-vous
                    <span class="badge red">{{ $rendezVousEnAttente ?? 0 }}</span>
                </a>
                <a class="navlink {{ request()->routeIs('admin.signalements.*') ? 'active' : '' }}"
                    href="{{ route('admin.signalements.index') }}">
                    <i class="ic fa-solid fa-flag"></i> Signalements
                    <span class="badge red">{{ $signalementsEnAttente ?? 0 }}</span>
                </a>
                <a class="navlink {{ request()->routeIs('admin.abonnements.*') ? 'active' : '' }}"
                    href="{{ route('admin.abonnements.index') }}">
                    <i class="ic fa-solid fa-award"></i> Abonnements
                </a>
                <a class="navlink {{ request()->routeIs('admin.quartiers.*') ? 'active' : '' }}"
                    href="{{ route('admin.quartiers.index') }}">
                    <i class="ic fa-solid fa-location-dot"></i> Quartiers
                </a>
                <div class="nav-group-label">Contenu</div>

                <a class="navlink {{ request()->routeIs('admin.bannieres.*') ? 'active' : '' }}"
                    href="{{ route('admin.bannieres.index') }}">
                    <i class="ic fa-solid fa-images"></i> Bannières
                    <span class="badge">{{ \App\Models\Banniere::where('est_actif', true)->count() }}</span>
                </a>

                <a class="navlink {{ request()->routeIs('admin.statistiques') ? 'active' : '' }}"
                    href="{{ route('admin.statistiques') }}">
                    <i class="ic fa-solid fa-chart-bar"></i> Statistiques
                </a>
            </div>

            <div class="nav-group-label">Compte</div>
            <div class="navlist">
                <a class="navlink {{ request()->routeIs('admin.profil') ? 'active' : '' }}"
                    href="{{ route('admin.profil') }}">
                    <i class="ic fa-solid fa-user-gear"></i> Mon profil
                </a>
                <form action="{{ route('logout') }}" method="POST" style="width:100%;">
                    @csrf
                    <button type="submit" class="navlink" style="width:100%;">
                        <i class="ic fa-solid fa-right-from-bracket"></i> Déconnexion
                    </button>
                </form>
            </div>

            <div class="sidebar-foot">
                <div class="agency-mini">
                    <div class="av" style="background:var(--gold);">AD</div>
                    <div>
                        <div class="name">{{ Auth::user()->prenom ?? 'Admin' }} {{ Auth::user()->nom ?? '' }}</div>
                        <div class="role">Super Admin</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main">
            <div class="topbar">
                <button class="burger" id="burgerBtn" onclick="toggleSidebar()">
                    <span></span><span></span><span></span>
                </button>
                <div class="topbar-left">
                    <div>
                        <div class="page-title" id="pageTitle">@yield('page_title', 'Vue d\'ensemble')</div>
                        <div class="page-sub" id="pageSub">@yield('page_sub', 'Activité globale de la plateforme DoyaImmo')</div>
                    </div>
                </div>
                <div class="top-actions">
                    <!-- ✅ Home Button -->
                    <a href="{{ route('home') }}" class="home-btn">
                        <i class="fa-solid fa-house"></i> Accueil
                    </a>
                    <div class="topbar-avatar" style="background:var(--gold); color:var(--ink);">AD</div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="flash-message flash-success">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="flash-message flash-error">
                <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            @if(session('info'))
            <div class="flash-message flash-info">
                <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
            </div>
            @endif

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>

    @stack('scripts')

</body>

</html>