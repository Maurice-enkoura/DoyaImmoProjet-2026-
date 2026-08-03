<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mon espace — DoyaImmo')</title>

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
            --teal: #0E7A7A;
            --teal-soft: rgba(14, 122, 122, 0.12);
            --gold: #D4AF37;
            --gold-soft: rgba(184, 150, 40, 0.12);
            --green: #1E7A47;
            --green-soft: rgba(30, 122, 71, 0.12);
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

        /* Sidebar */
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
        }

        .navlink .badge {
            margin-left: auto;
            background: var(--rust);
            color: #fff;
            font-size: 11px;
            padding: 1px 10px;
            border-radius: 999px;
        }

        .sidebar-foot {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 13.5px;
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
        }

        .btn-rust:hover {
            background: #9A4523;
            color: #fff;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-soft);
            border-color: var(--border);
        }

        .btn-ghost:hover {
            background: var(--border);
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 12.5px;
        }

        .btn-block {
            width: 100%;
            justify-content: center;
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
            color: #fff;
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

        /* Main */
        .main {
            margin-left: 260px;
            flex: 1;
            padding: 24px 32px 40px;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
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
            margin-left: auto;
        }

        .icon-btn-wrapper {
            position: relative;
            display: inline-block;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-soft);
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .icon-btn:hover {
            border-color: var(--rust);
            color: var(--rust);
        }

        .icon-btn .dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--rust);
            border: 2px solid #fff;
        }

        /* Dropdowns */
        .dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 360px;
            max-height: 400px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            z-index: 1000;
            padding: 8px 0;
        }

        .dropdown.open {
            display: block;
        }

        .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown-header button {
            background: none;
            border: none;
            color: var(--rust);
            font-size: 12px;
            cursor: pointer;
            font-weight: 600;
        }

        .dropdown-item {
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #F7F9FC;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-empty {
            padding: 30px 16px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .dropdown-empty i {
            font-size: 24px;
            display: block;
            margin-bottom: 8px;
            opacity: 0.3;
        }

        .topbar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Content */
        .content {
            max-width: 1200px;
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

        /* KPI Grid */
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

        .kpi-value {
            font-family: var(--display);
            font-weight: 700;
            font-size: 28px;
        }

        .kpi-label {
            font-size: 13px;
            color: var(--muted);
        }

        /* Grid 2 */
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

        /* Besoin Card (mini) */
        .besoin-card {
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

        /* Rendez-vous */
        .rdv-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .rdv-item:last-child {
            border-bottom: none;
        }

        .rdv-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: var(--border);
            border-radius: 10px;
            padding: 4px 12px;
            min-width: 48px;
        }

        .rdv-date .d {
            font-family: var(--display);
            font-weight: 700;
            font-size: 18px;
            line-height: 1.2;
        }

        .rdv-date .m {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--muted);
        }

        .rdv-info {
            flex: 1;
        }

        .rdv-info b {
            display: block;
            font-size: 14px;
        }

        .rdv-info span {
            font-size: 12.5px;
            color: var(--muted);
        }

        .rdv-actions {
            display: flex;
            gap: 8px;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-attente {
            background: #FFF8E1;
            color: #E65100;
        }

        .status-acceptee {
            background: #E8F5E9;
            color: #1E7A47;
        }

        .status-visite-prog {
            background: #E3F2FD;
            color: #0D47A1;
        }

        .status-contrat {
            background: #E8F5E9;
            color: #1E7A47;
        }

        /* Compare Table */
        .compare-wrap {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .compare-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .compare-head h3 {
            font-family: var(--display);
            font-size: 16px;
        }

        .compare-head .sub {
            font-size: 12.5px;
            color: var(--muted);
        }

        .compare-scroll {
            overflow-x: auto;
            padding: 0 4px 4px;
        }

        .compare-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 600px;
        }

        .compare-table th,
        .compare-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .compare-table th:first-child,
        .compare-table td:first-child {
            font-weight: 600;
            color: var(--text-soft);
            width: 120px;
        }

        .compare-table .best-col {
            background: #F0FDF4;
        }

        .compare-table .best-col b {
            color: var(--green);
        }

        .compare-table .compare-selected {
            background: #E3F2FD !important;
        }

        .compare-table .compare-faded {
            opacity: 0.5;
        }

        .agency-col-name {
            font-weight: 600;
            font-size: 13px;
        }

        .agency-col-badge {
            font-size: 10px;
            color: var(--muted);
            font-weight: 400;
        }

        .compare-cta-row td {
            padding: 12px 16px;
        }

        /* Rating */
        .rating-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .rating-row:last-child {
            border-bottom: none;
        }

        .rating-row span {
            font-size: 13px;
            font-weight: 500;
            min-width: 140px;
        }

        .star-input {
            display: flex;
            gap: 4px;
        }

        .star-input i {
            font-size: 18px;
            color: #D4D8E0;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-input i.active {
            color: #F5A623;
        }

        /* Timeline */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .tl-row {
            display: flex;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .tl-row:last-child {
            border-bottom: none;
        }

        .tl-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .tl-content .when {
            font-size: 12px;
            color: var(--muted);
        }

        .tl-content .what {
            font-size: 14px;
        }

        /* Slot */
        .slot-day {
            font-weight: 600;
            font-size: 14px;
            margin: 12px 0 8px;
        }

        .slot-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .slot-btn {
            padding: 8px 20px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }

        .slot-btn:hover {
            border-color: var(--rust);
        }

        .slot-btn.selected {
            background: var(--rust);
            color: #fff;
            border-color: var(--rust);
        }

        /* Avis */
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

        /* Responsive */
        @media (max-width: 820px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
                padding: 20px;
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

            .dropdown {
                width: 300px;
                right: -40px;
            }
        }

        @media (max-width: 480px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }

            .compare-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .rdv-item {
                flex-wrap: wrap;
            }

            .dropdown {
                width: 280px;
                right: -60px;
            }
        }

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

        /* Flash messages */
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

        .field {
            margin-bottom: 12px;
        }

        .field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-soft);
            margin-bottom: 4px;
        }

        .field textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            transition: border 0.2s;
            resize: vertical;
            min-height: 80px;
        }

        .field textarea:focus {
            outline: none;
            border-color: var(--rust);
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('home') }}" class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span>Immo</span></div>
            </a>

            <div class="nav-group-label">Mon espace</div>
            <div class="navlist">
                <a class="navlink {{ request()->routeIs('particulier.dashboard') ? 'active' : '' }}"
                    href="{{ route('particulier.dashboard') }}">
                    <i class="ic fa-solid fa-gauge"></i> Tableau de bord
                </a>
                <a class="navlink {{ request()->routeIs('particulier.demandes.*') ? 'active' : '' }}"
                    href="{{ route('particulier.demandes.index') }}">
                    <i class="ic fa-solid fa-house-circle-check"></i> Mes besoins publiés
                    <span class="badge" id="besoinBadge">{{ $besoinsCount ?? 0 }}</span>
                </a>
                <a class="navlink {{ request()->routeIs('particulier.propositions.*') ? 'active' : '' }}"
                    href="{{ route('particulier.propositions.index') }}">
                    <i class="ic fa-solid fa-file-invoice"></i> Offres reçues
                    <span class="badge" id="offreBadge">{{ $offresCount ?? 0 }}</span>
                </a>
                <a class="navlink {{ request()->routeIs('particulier.rendezvous.*') ? 'active' : '' }}"
                    href="{{ route('particulier.rendezvous.index') }}">
                    <i class="ic fa-solid fa-calendar-days"></i> Rendez-vous
                </a>
                <a class="navlink {{ request()->routeIs('particulier.evaluations.*') ? 'active' : '' }}"
                    href="{{ route('particulier.evaluations.index') }}">
                    <i class="ic fa-solid fa-star"></i> Mes avis donnés
                </a>
                <a class="navlink {{ request()->routeIs('particulier.historique') ? 'active' : '' }}"
                    href="{{ route('particulier.historique') }}">
                    <i class="ic fa-solid fa-clock-rotate-left"></i> Historique
                </a>
            </div>

            <div class="nav-group-label">Compte</div>
            <div class="navlist">
                <a class="navlink" href="{{ route('particulier.profil') }}">
                    <i class="ic fa-solid fa-user"></i> Mon profil
                </a>
                <form action="{{ route('logout') }}" method="POST" style="width:100%;">
                    @csrf
                    <button type="submit" class="navlink" style="width:100%;">
                        <i class="ic fa-solid fa-right-from-bracket"></i> Déconnexion
                    </button>
                </form>
            </div>

            <div class="sidebar-foot">
                <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-block" style="margin-bottom:14px;">
                    + Publier un besoin
                </a>
                <div class="agency-mini">
                    <div class="av" style="background:var(--rust);">
                        {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
                    </div>
                    <div>
                        <div class="name">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                        <div class="role">Compte client</div>
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
                <div>
                    <div class="page-title" id="pageTitle">@yield('page_title', 'Tableau de bord')</div>
                    <div class="page-sub" id="pageSub">@yield('page_sub', 'Suivez vos besoins et vos échanges avec les agences')</div>
                </div>

                <div class="top-actions">
                    <!-- Notifications -->
                    <div class="icon-btn-wrapper">
                        <button class="icon-btn" id="notificationBtn" onclick="toggleNotifications()">
                            <i class="fa-regular fa-bell" style="color:#20262F;"></i>
                            @if($notificationsCount ?? 0 > 0)
                                <span class="dot"></span>
                            @endif
                        </button>
                        <div class="dropdown" id="notificationDropdown">
                            <div class="dropdown-header">
                                <span>Notifications</span>
                                @if($notificationsCount ?? 0 > 0)
                                    <button onclick="markAllAsRead()">Tout marquer comme lu</button>
                                @endif
                            </div>
                            <div id="notificationList">
                                @if($notificationsCount ?? 0 > 0)
                                    @foreach($notifications as $notification)
                                        <div class="dropdown-item" onclick="markNotificationRead('{{ $notification->id }}')">
                                            <div style="font-size:13px;color:var(--text-soft);">{{ $notification->data['message'] ?? 'Nouvelle notification' }}</div>
                                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="dropdown-empty">
                                        <i class="fa-regular fa-bell-slash"></i>
                                        Aucune notification
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Messagerie -->
                    <div class="icon-btn-wrapper">
                        <button class="icon-btn" id="messageBtn" onclick="toggleMessages()">
                            <i class="fa-solid fa-envelope" style="color:#20262F;"></i>
                            @if($messagesCount ?? 0 > 0)
                                <span class="dot"></span>
                            @endif
                        </button>
                        <div class="dropdown" id="messageDropdown">
                            <div class="dropdown-header">
                                <span>Messages</span>
                            </div>
                            @if($messagesCount ?? 0 > 0)
                                @foreach($messages as $message)
                                    <div class="dropdown-item">
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div style="width:32px;height:32px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:var(--rust);flex-shrink:0;">
                                                {{ strtoupper(substr($message['sender'], 0, 1)) }}
                                            </div>
                                            <div style="flex:1;min-width:0;">
                                                <div style="font-weight:600;font-size:13px;">{{ $message['sender'] }}</div>
                                                <div style="font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $message['preview'] }}</div>
                                            </div>
                                            <div style="font-size:10px;color:var(--muted);flex-shrink:0;">{{ $message['time'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="dropdown-empty">
                                    <i class="fa-regular fa-envelope"></i>
                                    Aucun message
                                </div>
                            @endif
                            <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
                                <a href="#" style="color:var(--rust);font-size:12px;font-weight:600;text-decoration:none;">Voir tous les messages</a>
                            </div>
                        </div>
                    </div>

                    <div class="topbar-avatar" style="background:var(--rust);">
                        {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
                    </div>
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

        // Toggle notifications
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const messageDropdown = document.getElementById('messageDropdown');
            messageDropdown.classList.remove('open');
            dropdown.classList.toggle('open');
        }

        // Toggle messages
        function toggleMessages() {
            const dropdown = document.getElementById('messageDropdown');
            const notificationDropdown = document.getElementById('notificationDropdown');
            notificationDropdown.classList.remove('open');
            dropdown.classList.toggle('open');
        }

        // Fermer les dropdowns en cliquant ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.icon-btn-wrapper')) {
                document.getElementById('notificationDropdown').classList.remove('open');
                document.getElementById('messageDropdown').classList.remove('open');
            }
        });

        // Marquer une notification comme lue
        function markNotificationRead(id) {
            fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      location.reload();
                  }
              });
        }

        // Marquer toutes les notifications comme lues
        function markAllAsRead() {
            fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      location.reload();
                  }
              });
        }

        // Récupérer le nombre de notifications (AJAX)
        function refreshNotificationCount() {
            fetch('/api/notifications/count', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(response => response.json())
              .then(data => {
                  const dot = document.querySelector('.icon-btn .dot');
                  if (data.count > 0) {
                      if (!dot) {
                          const btn = document.querySelector('.icon-btn .fa-bell').closest('.icon-btn');
                          const dotEl = document.createElement('span');
                          dotEl.className = 'dot';
                          btn.appendChild(dotEl);
                      }
                  } else {
                      const dot = document.querySelector('.icon-btn .dot');
                      if (dot) dot.remove();
                  }
              });
        }

        // Rafraîchir toutes les 30 secondes
        setInterval(refreshNotificationCount, 30000);
    </script>

    @stack('scripts')

</body>

</html>