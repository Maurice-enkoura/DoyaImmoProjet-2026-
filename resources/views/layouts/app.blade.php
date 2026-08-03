<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Styles personnalisés -->
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
            --gold-soft: rgba(184, 150, 40, 0.12);
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

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .section {
            padding: 60px 0;
        }

        /* Header */
        .pub-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .pub-nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
            flex-shrink: 0;
        }

        .brand-mark {
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

        .brand-name {
            font-family: var(--display);
            font-weight: 700;
            font-size: 18px;
        }

        .brand-name span {
            color: var(--rust);
        }

        /* Search Bar */
        .search-header {
            flex: 1;
            max-width: 400px;
            margin: 0 16px;
            min-width: 180px;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 13px;
            pointer-events: none;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            background: #F7F9FC;
            transition: all 0.2s;
            font-family: inherit;
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--rust);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.1);
        }

        .search-input-wrapper input::placeholder {
            color: var(--muted);
        }

        /* Autocomplete */
        #headerAutocomplete {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            z-index: 1000;
            display: none;
            max-height: 400px;
            overflow-y: auto;
        }

        #headerAutocomplete .autocomplete-item {
            padding: 10px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        #headerAutocomplete .autocomplete-item:last-child {
            border-bottom: none;
        }

        #headerAutocomplete .autocomplete-item:hover {
            background: #F7F9FC;
        }

        #headerAutocomplete .autocomplete-item .item-icon {
            color: var(--rust);
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        #headerAutocomplete .autocomplete-item .item-content {
            flex: 1;
            min-width: 0;
        }

        #headerAutocomplete .autocomplete-item .item-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--ink);
        }

        #headerAutocomplete .autocomplete-item .item-desc {
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #headerAutocomplete .autocomplete-item .item-type {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--border);
            padding: 2px 10px;
            border-radius: 999px;
            flex-shrink: 0;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        #headerAutocomplete .autocomplete-empty {
            padding: 20px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        /* Search shortcut */
        .search-shortcut {
            display: none;
            font-size: 11px;
            color: var(--muted);
            background: var(--border);
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        @media (min-width: 768px) {
            .search-shortcut {
                display: block;
            }
        }

        /* Navigation Links */
        .pub-links {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .pub-links a {
            color: var(--text-soft);
            text-decoration: none;
            font-size: 13.5px;
            transition: color 0.2s;
            white-space: nowrap;
        }

        .pub-links a:hover {
            color: var(--rust);
        }

        .pub-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-shrink: 0;
        }

        .pub-burger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            flex-shrink: 0;
        }

        .pub-burger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--ink);
            margin: 4px 0;
            transition: 0.2s;
        }

        .pub-mobile-menu {
            display: none;
            padding: 16px 24px;
            background: #fff;
            border-top: 1px solid var(--border);
        }

        .pub-mobile-menu a {
            display: block;
            padding: 10px 0;
            color: var(--text-soft);
            text-decoration: none;
            font-size: 14px;
        }

        .pub-mobile-menu .mobile-search {
            margin-bottom: 12px;
        }

        .pub-mobile-menu .mobile-search input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .pub-links {
                gap: 16px;
            }
            .pub-links a {
                font-size: 13px;
            }
        }

        @media (max-width: 820px) {
            .pub-links, .pub-actions .btn-ghost {
                display: none;
            }
            .pub-burger {
                display: block;
            }
            .pub-mobile-menu.open {
                display: block;
            }
            .search-header {
                max-width: 280px;
                margin: 0 12px;
            }
        }

        @media (max-width: 600px) {
            .pub-nav {
                padding: 10px 16px;
                flex-wrap: wrap;
            }
            .search-header {
                order: 3;
                flex: 1 1 100%;
                max-width: 100%;
                margin: 8px 0 0;
            }
            .pub-actions .btn-rust {
                padding: 6px 12px;
                font-size: 12px;
            }
            .brand-name {
                font-size: 16px;
            }
            .brand-mark {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }
        }

        /* Buttons */
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

        .btn-lg {
            padding: 14px 28px;
            font-size: 15px;
        }

        .btn-block {
            width: 100%;
            justify-content: center;
        }

        /* Cards */
        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }

        .eyebrow {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            color: var(--rust);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .h-hero {
            font-family: var(--display);
            font-weight: 800;
            font-size: clamp(32px, 4.5vw, 52px);
            line-height: 1.15;
        }

        .h-section {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(24px, 3vw, 34px);
            line-height: 1.2;
        }

        .lead {
            font-size: 17px;
            color: var(--text-soft);
            line-height: 1.7;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--border);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            color: var(--text-soft);
        }

        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-bottom: 24px;
        }

        .filter-bar select,
        .filter-bar input {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            font-size: 13px;
            font-family: inherit;
            color: var(--ink);
            min-width: 140px;
        }

        .filter-bar .grow {
            flex: 1;
            min-width: 160px;
        }

        /* Besoin Grid */
        .besoin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .besoin-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .besoin-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.06);
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
            padding: 20px;
        }

        .besoin-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .besoin-type {
            font-family: var(--display);
            font-weight: 700;
            font-size: 16px;
        }

        .besoin-budget {
            font-weight: 700;
            color: var(--rust);
            font-size: 14px;
        }

        .besoin-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .besoin-desc {
            font-size: 13.5px;
            color: var(--text-soft);
            line-height: 1.6;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

        /* Footer */
        .pub-footer {
            background: var(--ink);
            color: #fff;
            padding: 48px 0 24px;
            margin-top: 60px;
        }

        .footer-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        @media (max-width: 820px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 520px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        .footer-grid h4 {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #B9BFCC;
            margin-bottom: 12px;
        }

        .footer-grid ul {
            list-style: none;
            padding: 0;
        }

        .footer-grid ul li {
            margin-bottom: 8px;
        }

        .footer-grid ul li a {
            color: #8A91A0;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }

        .footer-grid ul li a:hover {
            color: #fff;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            color: #6A7280;
        }

        @media (max-width: 600px) {
            .footer-bottom {
                flex-direction: column;
                gap: 6px;
                text-align: center;
            }
        }

        /* Biens Grid */
        .biens-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .bien-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .bien-card:hover {
            transform: translateY(-3px);
        }

        .bien-image {
            height: 200px;
            background: var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 14px;
            position: relative;
        }

        .bien-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .bien-body {
            padding: 16px 20px 20px;
        }

        .bien-title {
            font-family: var(--display);
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 6px;
        }

        .bien-price {
            font-weight: 700;
            color: var(--rust);
            font-size: 16px;
        }

        .bien-infos {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--text-soft);
        }

        .bien-infos span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Zone chips */
        .zone-row {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .zone-chip {
            flex-shrink: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 22px;
            text-align: center;
            min-width: 130px;
        }

        .zone-chip b {
            display: block;
            font-family: var(--display);
            font-size: 14px;
            margin-bottom: 3px;
        }

        .zone-chip span {
            font-size: 11.5px;
            color: var(--muted);
        }

        /* Hero */
        .hero {
            padding: 64px 0 90px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            right: -140px;
            top: -100px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181,80,42,.12), transparent 70%);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 50px;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        @media (max-width: 900px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }
        }

        .hero-illustration {
            background: var(--ink);
            border-radius: 20px;
            padding: 26px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .hero-illustration::before {
            content: "";
            position: absolute;
            left: -60px;
            bottom: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(18,122,122,.35), transparent 70%);
        }

        .mini-card {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .mini-card .t {
            font-family: var(--display);
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .mini-card .s {
            font-size: 12px;
            color: #9AA1AB;
        }

        .mini-card:last-child {
            margin-bottom: 0;
        }

        /* How it works */
        .how-grid {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 24px;
            margin-top: 36px;
        }

        @media (max-width: 760px) {
            .how-grid {
                grid-template-columns: 1fr;
            }
        }

        .how-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 26px;
        }

        .how-num {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--rust-soft);
            color: var(--rust);
            font-family: var(--display);
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .how-card h3 {
            font-family: var(--display);
            font-size: 16px;
            margin-bottom: 8px;
        }

        .how-card p {
            font-size: 13.5px;
            color: var(--text-soft);
            line-height: 1.65;
        }

        /* Value grid */
        .value-grid {
            display: grid;
            grid-template-columns: repeat(4,1fr);
            gap: 20px;
            margin-top: 30px;
        }

        @media (max-width: 900px) {
            .value-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 520px) {
            .value-grid {
                grid-template-columns: 1fr;
            }
        }

        .value-card {
            padding: 22px 0;
        }

        .value-ic {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            font-size: 19px;
        }

        .value-card h3 {
            font-family: var(--display);
            font-size: 15px;
            margin-bottom: 6px;
        }

        .value-card p {
            font-size: 13px;
            color: var(--text-soft);
            line-height: 1.6;
        }

        /* CTA Band */
        .cta-band {
            background: var(--ink);
            border-radius: 24px;
            padding: 56px;
            text-align: center;
            color: #fff;
        }

        @media (max-width: 640px) {
            .cta-band {
                padding: 36px 24px;
            }
        }

        .cta-band h2 {
            font-family: var(--display);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 14px;
        }

        .cta-band p {
            font-size: 14.5px;
            color: #B9BFCC;
            max-width: 480px;
            margin: 0 auto 26px;
        }

        .cta-actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Detail page */
        .detail-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        .detail-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13.5px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row span:first-child {
            color: var(--muted);
        }

        .info-row span:last-child {
            font-weight: 600;
        }

        .offer-count {
            background: var(--teal-soft);
            color: var(--teal);
            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 999px;
        }

        .sticky-box {
            position: sticky;
            top: 100px;
        }

        .results-count {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 18px;
        }

        .page-head {
            padding: 44px 0 10px;
        }

        /* Flash messages */
        .flash-message {
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 14px;
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
    </style>

    @stack('styles')
</head>
<body>

<!-- Header -->
<header class="pub-header">
    <nav class="pub-nav">
        <a href="{{ route('home') }}" class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name">Doya<span>Immo</span></div>
        </a>

        <!-- Barre de recherche -->
        <div class="search-header">
            <div class="search-input-wrapper">
                <i class="fa-solid fa-search search-icon"></i>
                <input 
                    type="text" 
                    id="headerSearch" 
                    placeholder="Rechercher un besoin, un bien, une agence..."
                    autocomplete="off"
                    aria-label="Recherche"
                >
                <span class="search-shortcut">⌘K</span>
                <div id="headerAutocomplete"></div>
            </div>
        </div>

        <div class="pub-links">
            <a href="{{ route('besoins.index') }}">Besoins</a>
            <a href="{{ route('biens.index') }}">Biens</a>
            <a href="{{ route('home') }}#comment">Comment ça marche</a>
            <a href="{{ route('register.agence') }}">Pour les agences</a>
        </div>

        <div class="pub-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-user"></i> {{ Auth::user()->prenom ?? 'Profil' }}
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Connexion</a>
                <a href="{{ route('register') }}" class="btn btn-rust btn-sm">
                    <i class="fa-solid fa-plus"></i> Publier
                </a>
            @endauth
        </div>

        <button class="pub-burger" id="pubBurger" onclick="toggleMobileMenu()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- Menu mobile -->
    <div class="pub-mobile-menu" id="pubMobileMenu">
        <div class="mobile-search">
            <input type="text" id="mobileSearch" placeholder="Rechercher..." oninput="syncSearch(this)">
        </div>
        <a href="{{ route('besoins.index') }}">Parcourir les besoins</a>
        <a href="{{ route('biens.index') }}">Parcourir les biens</a>
        <a href="{{ route('home') }}#comment">Comment ça marche</a>
        <a href="{{ route('register.agence') }}">Pour les agences</a>
        @auth
            <a href="{{ route('dashboard') }}">Mon tableau de bord</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:none;border:none;color:var(--text-soft);font-size:14px;padding:10px 0;cursor:pointer;width:100%;text-align:left;">
                    Déconnexion
                </button>
            </form>
        @else
            <a href="{{ route('login') }}">Connexion</a>
            <a href="{{ route('register.particulier') }}">Publier un besoin</a>
        @endauth
    </div>
</header>

<!-- Flash Messages -->
@if(session('success'))
    <div class="wrap" style="padding-top:20px;">
        <div class="flash-message flash-success">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="wrap" style="padding-top:20px;">
        <div class="flash-message flash-error">
            <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    </div>
@endif

@if(session('info'))
    <div class="wrap" style="padding-top:20px;">
        <div class="flash-message flash-info">
            <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
        </div>
    </div>
@endif

<!-- Contenu principal -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="pub-footer">
    <div class="footer-wrap">
        <div class="footer-grid">
            <div>
                <div class="brand" style="margin-bottom:14px;">
                    <div class="brand-mark">D</div>
                    <div class="brand-name" style="color:#fff;">Doya<span>Immo</span></div>
                </div>
                <p style="font-size:13px; color:#8A91A0; line-height:1.7; max-width:260px;">
                    La plateforme de mise en relation immobilière entre clients et agences à Dakar.
                </p>
            </div>
            <div>
                <h4>Plateforme</h4>
                <ul>
                    <li><a href="{{ route('besoins.index') }}">Parcourir les besoins</a></li>
                    <li><a href="{{ route('register.particulier') }}">Créer un compte</a></li>
                    <li><a href="{{ route('register.agence') }}">Devenir agence partenaire</a></li>
                </ul>
            </div>
            <div>
                <h4>Ressources</h4>
                <ul>
                    <li><a href="#">Conditions d'utilisation</a></li>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:contact@doyaimmo.sn">contact@doyaimmo.sn</a></li>
                    <li><a href="tel:+221338000000">+221 33 800 00 00</a></li>
                    <li>Dakar, Sénégal</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} DoyaImmo — Tous droits réservés</span>
            <span>Fait à Dakar 🇸🇳</span>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script>
    // Toggle mobile menu
    function toggleMobileMenu() {
        const menu = document.getElementById('pubMobileMenu');
        menu.classList.toggle('open');
    }

    // Fermer le menu mobile au clic sur un lien
    document.querySelectorAll('#pubMobileMenu a').forEach(link => {
        link.addEventListener('click', () => {
            document.getElementById('pubMobileMenu').classList.remove('open');
        });
    });

    // Sync search between desktop and mobile
    function syncSearch(mobileInput) {
        const desktopSearch = document.getElementById('headerSearch');
        desktopSearch.value = mobileInput.value;
        // Trigger input event for autocomplete
        desktopSearch.dispatchEvent(new Event('input'));
    }

    // Recherche autocomplete
    document.addEventListener('DOMContentLoaded', function() {
        const headerSearch = document.getElementById('headerSearch');
        const headerResults = document.getElementById('headerAutocomplete');
        let debounceTimer;

        // Keyboard shortcut: Cmd+K or Ctrl+K
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                headerSearch.focus();
                headerSearch.select();
            }
            // Escape to close autocomplete
            if (e.key === 'Escape') {
                headerResults.style.display = 'none';
                headerSearch.blur();
            }
        });

        headerSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            
            if (query.length < 2) {
                headerResults.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                // Simulation d'API - À remplacer par votre endpoint réel
                // fetch(`/api/search/autocomplete?q=${encodeURIComponent(query)}`)
                //     .then(response => response.json())
                //     .then(data => {
                //         renderResults(data);
                //     })
                //     .catch(() => {
                //         headerResults.style.display = 'none';
                //     });

                // Données de test (à remplacer par votre API)
                const mockData = [
                    { 
                        label: 'Appartement 3 pièces', 
                        description: 'Almadies, Dakar', 
                        icon: 'fa-solid fa-building',
                        type: 'Bien',
                        url: '/biens/1'
                    },
                    { 
                        label: 'Studio meublé', 
                        description: 'Mermoz, Dakar', 
                        icon: 'fa-solid fa-home',
                        type: 'Bien',
                        url: '/biens/2'
                    },
                    { 
                        label: 'Recherche villa 4 pièces', 
                        description: 'Ngor, Dakar', 
                        icon: 'fa-solid fa-magnifying-glass',
                        type: 'Besoin',
                        url: '/besoins/1'
                    },
                    { 
                        label: 'Teranga Immobilier', 
                        description: 'Agence à Almadies', 
                        icon: 'fa-solid fa-building-columns',
                        type: 'Agence',
                        url: '/agences/1'
                    }
                ];

                const filtered = mockData.filter(item => 
                    item.label.toLowerCase().includes(query.toLowerCase()) ||
                    item.description.toLowerCase().includes(query.toLowerCase())
                );

                renderResults(filtered);
            }, 300);
        });

        function renderResults(data) {
            if (!data || data.length === 0) {
                headerResults.innerHTML = `
                    <div class="autocomplete-empty">
                        <i class="fa-solid fa-search" style="display:block;font-size:20px;margin-bottom:8px;color:var(--muted);"></i>
                        Aucun résultat trouvé
                    </div>
                `;
                headerResults.style.display = 'block';
                return;
            }

            headerResults.innerHTML = data.map(item => `
                <div class="autocomplete-item" onclick="window.location.href='${item.url || '#'}'">
                    <i class="${item.icon} item-icon"></i>
                    <div class="item-content">
                        <div class="item-title">${item.label}</div>
                        <div class="item-desc">${item.description || ''}</div>
                    </div>
                    <span class="item-type">${item.type}</span>
                </div>
            `).join('');

            headerResults.style.display = 'block';
        }

        // Fermer l'autocomplete en cliquant ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-input-wrapper')) {
                headerResults.style.display = 'none';
            }
        });

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                headerResults.style.display = 'none';
            }
        });
    });
</script>

@stack('scripts')

</body>
</html>