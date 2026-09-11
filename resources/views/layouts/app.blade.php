<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ═══════════════════════════════════════════
         SEO
    ═══════════════════════════════════════════ --}}
    <title>@yield('title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')</title>
    <meta name="description" content="@yield('meta_description', 'DoyaImmo est une plateforme immobilière basée à Dakar qui met en relation particuliers et agences immobilières pour la recherche, la location et la vente de logements.')">
    <link rel="canonical" href="@yield('canonical', 'https://doyaimmo.com'.request()->getRequestUri())">
    <meta name="robots" content="@yield('robots', 'index, follow')">

    {{-- Favicons --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="DoyaImmo">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')">
    <meta property="og:description" content="@yield('og_description', 'DoyaImmo met en relation particuliers et agences immobilières à Dakar.')">
    <meta property="og:url" content="@yield('canonical', 'https://doyaimmo.com'.request()->getRequestUri())">
    <meta property="og:image" content="{{ asset('logo-og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="fr_SN">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@doyaimmo">
    <meta name="twitter:title" content="@yield('og_title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')">
    <meta name="twitter:description" content="@yield('og_description', 'DoyaImmo met en relation particuliers et agences immobilières à Dakar.')">
    <meta name="twitter:image" content="{{ asset('logo-og.png') }}">

    {{-- Anti-flash (simplifié) --}}
    <style>
        #app { opacity: 0; transition: opacity .25s ease; }
        #app.ready { opacity: 1; }
        html, body { background: #F7F9FC; min-height: 100vh; }
    </style>

    {{-- Fonts (async) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"></noscript>

    {{-- Font Awesome (async) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════════════════════
           RESET & VARIABLES
        ═══════════════════════════════════════════════════════════ */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --rust: #B5502A;
            --rust-soft: rgba(181, 80, 42, .12);
            --ink: #1A1D26;
            --surface: #FFFFFF;
            --border: #E8ECF0;
            --text-soft: #4A5260;
            --muted: #8A91A0;
            --teal: #0E7A7A;
            --gold: #D4AF37;
            --green: #1E7A47;
            --green-soft: rgba(30, 122, 71, .12);
            --radius: 16px;
            --radius-sm: 10px;
            --display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --header-height: 72px;
            --container-padding: clamp(16px, 4vw, 24px);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, .04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, .08);
            --shadow-lg: 0 16px 48px rgba(0, 0, 0, .12);
        }

        body {
            font-family: var(--display);
            background: #F7F9FC;
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
        }

        img, svg, video { max-width: 100%; height: auto; display: block; }

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--container-padding);
            width: 100%;
        }

        .section { padding: clamp(40px, 8vw, 60px) 0; }

        /* ═══════════════════════════════════════════════════════════
           HEADER
        ═══════════════════════════════════════════════════════════ */
        .pub-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            width: 100%;
        }

        .pub-nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 12px var(--container-padding);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: clamp(10px, 2vw, 18px);
            min-height: var(--header-height);
        }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 9px;
            text-decoration: none;
            color: var(--ink);
            flex-shrink: 0;
        }

        .brand-mark {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            color: #fff;
            font-family: var(--display);
            font-weight: 800;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(181, 80, 42, .25);
            transition: transform .2s;
        }
        .brand:hover .brand-mark { transform: scale(1.05); }

        .brand-name {
            font-family: var(--display);
            font-weight: 700;
            font-size: 18px;
            letter-spacing: -.3px;
        }
        .brand-name span { color: var(--rust); }

        /* ═══════════════════════════════════════════════════════════
           SEARCH HEADER (desktop)
        ═══════════════════════════════════════════════════════════ */
        .search-header {
            flex: 1;
            max-width: 520px;
            min-width: 180px;
        }

        .search-input-wrapper {
            position: relative;
            width: 100%;
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 14px;
            pointer-events: none;
            z-index: 2;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 11px 90px 11px 44px;
            border: 1.5px solid var(--border);
            border-radius: 999px;
            font-size: 14px;
            background: #F7F9FC;
            transition: all .25s ease;
            font-family: var(--display);
            color: var(--ink);
            -webkit-appearance: none;
            appearance: none;
        }
        .search-input-wrapper input::placeholder {
            color: var(--muted);
            font-size: 13.5px;
        }
        .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--rust);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(181, 80, 42, .08);
        }

        .search-shortcut {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: var(--muted);
            background: #fff;
            padding: 3px 9px;
            border-radius: 6px;
            font-weight: 700;
            pointer-events: none;
            border: 1px solid var(--border);
            letter-spacing: .3px;
        }

        /* Autocomplete */
        #headerAutocomplete {
            position: absolute;
            top: calc(100% + 8px);
            left: 0; right: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            display: none;
            max-height: 420px;
            overflow-y: auto;
            padding: 8px;
            animation: dropdownIn .2s ease;
        }
        #headerAutocomplete.is-open { display: block; }

        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        #headerAutocomplete .autocomplete-item {
            padding: 10px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 10px;
            transition: background .15s;
        }
        #headerAutocomplete .autocomplete-item:hover { background: #F7F9FC; }

        #headerAutocomplete .item-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        #headerAutocomplete .item-icon.besoin   { background: #E3F2FD; color: #0D47A1; }
        #headerAutocomplete .item-icon.bien     { background: #E8F5E9; color: #1E7A47; }
        #headerAutocomplete .item-icon.agence   { background: #FFF8E1; color: #E65100; }
        #headerAutocomplete .item-icon.quartier { background: #F3E5F5; color: #4A148C; }

        #headerAutocomplete .item-content { flex: 1; min-width: 0; }
        #headerAutocomplete .item-title {
            font-weight: 600;
            font-size: 13.5px;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #headerAutocomplete .item-desc {
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #headerAutocomplete .item-type {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--muted);
            background: #F7F9FC;
            padding: 3px 10px;
            border-radius: 999px;
            flex-shrink: 0;
            font-weight: 700;
            letter-spacing: .4px;
        }

        #headerAutocomplete .autocomplete-empty {
            padding: 30px 20px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }
        #headerAutocomplete .autocomplete-empty i {
            font-size: 28px;
            display: block;
            margin-bottom: 10px;
            opacity: .3;
        }

        /* ═══════════════════════════════════════════════════════════
           LIENS DESKTOP
        ═══════════════════════════════════════════════════════════ */
        .pub-links {
            display: flex;
            gap: 22px;
            align-items: center;
        }

        .pub-links a {
            color: var(--text-soft);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: color .2s;
            white-space: nowrap;
            position: relative;
            padding: 4px 0;
        }
        .pub-links a::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 0; height: 2px;
            background: var(--rust);
            border-radius: 2px;
            transition: width .25s ease;
        }
        .pub-links a:hover { color: var(--rust); }
        .pub-links a:hover::after { width: 100%; }

        /* ═══════════════════════════════════════════════════════════
           ACTIONS
        ═══════════════════════════════════════════════════════════ */
        .pub-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-shrink: 0;
        }

        /* Burger */
        .pub-burger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            flex-shrink: 0;
            border-radius: 9px;
            transition: background .2s;
        }
        .pub-burger:hover { background: var(--border); }
        .pub-burger span {
            display: block;
            width: 22px; height: 2px;
            background: var(--ink);
            margin: 4px 0;
            transition: .3s;
            border-radius: 2px;
        }
        .pub-burger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .pub-burger.active span:nth-child(2) { opacity: 0; }
        .pub-burger.active span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }

        /* ═══════════════════════════════════════════════════════════
           MOBILE : recherche + burger
        ═══════════════════════════════════════════════════════════ */
        .mobile-icons {
            display: none;
            align-items: center;
            gap: 6px;
        }

        .search-toggle-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 9px;
            border-radius: 9px;
            color: var(--text-soft);
            font-size: 17px;
            transition: all .2s;
        }
        .search-toggle-btn:hover { background: var(--border); color: var(--rust); }

        /* ═══════════════════════════════════════════════════════════
           MENU MOBILE
        ═══════════════════════════════════════════════════════════ */
        .pub-mobile-menu {
            display: none;
            position: fixed;
            top: var(--header-height);
            left: 0; right: 0;
            background: #fff;
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            z-index: 99;
            max-height: calc(100vh - var(--header-height));
            overflow-y: auto;
            padding: 12px var(--container-padding) 20px;
            transform: translateY(-8px);
            opacity: 0;
            visibility: hidden;
            transition: all .25s ease;
        }
        .pub-mobile-menu.open {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .pub-mobile-menu .menu-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 14px;
            color: var(--text-soft);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border-radius: 12px;
            transition: all .2s;
            border: none;
            background: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }
        .pub-mobile-menu .menu-item:hover,
        .pub-mobile-menu .menu-item:active {
            background: var(--rust-soft);
            color: var(--rust);
        }

        .pub-mobile-menu .menu-item .menu-icon {
            width: 34px; height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: var(--rust);
            background: var(--rust-soft);
            flex-shrink: 0;
            transition: all .2s;
        }
        .pub-mobile-menu .menu-item .menu-text { flex: 1; font-weight: 600; }

        .pub-mobile-menu .menu-divider {
            height: 1px;
            background: var(--border);
            margin: 10px 4px;
        }

        .pub-mobile-menu .menu-auth {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }
        .pub-mobile-menu .menu-auth .menu-item {
            flex: 1;
            justify-content: center;
            background: var(--rust);
            color: #fff;
            font-weight: 600;
            padding: 13px;
        }
        .pub-mobile-menu .menu-auth .menu-item:hover {
            background: #9A4523;
            color: #fff;
        }
        .pub-mobile-menu .menu-auth .menu-item .menu-icon {
            background: rgba(255, 255, 255, .18);
            color: #fff;
        }

        /* ═══════════════════════════════════════════════════════════
           OVERLAY RECHERCHE (mobile)
        ═══════════════════════════════════════════════════════════ */
        .search-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 999;
            backdrop-filter: blur(4px);
            animation: fadeIn .25s ease;
        }
        .search-overlay.active { display: block; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .search-overlay .search-modal {
            position: absolute;
            top: 0; left: 0; right: 0;
            background: #fff;
            padding: 20px 22px 24px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            animation: slideDown .3s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-30px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        .search-overlay .search-close {
            position: absolute;
            right: 16px; top: 16px;
            background: #F7F9FC;
            border: none;
            width: 36px; height: 36px;
            border-radius: 50%;
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all .2s;
        }
        .search-overlay .search-close:hover { background: var(--border); color: var(--ink); }

        .search-overlay .search-input-wrapper { margin-top: 8px; }
        .search-overlay .search-input-wrapper input {
            font-size: 16px;
            padding: 14px 20px 14px 48px;
            border-radius: 12px;
        }
        .search-overlay .search-input-wrapper .search-icon {
            left: 16px;
            font-size: 16px;
        }

        .search-overlay .search-suggestions { margin-top: 18px; }
        .search-overlay .suggestion-title {
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 10px;
        }
        .search-overlay .suggestion-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 10px;
            color: var(--text-soft);
            text-decoration: none;
            transition: background .15s;
            font-size: 14px;
            font-weight: 500;
        }
        .search-overlay .suggestion-item:hover { background: #F7F9FC; }
        .search-overlay .suggestion-item i {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: var(--rust-soft);
            color: var(--rust);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ═══════════════════════════════════════════════════════════
           BUTTONS
        ═══════════════════════════════════════════════════════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 11px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s ease;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
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
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
        }
        .btn-ghost {
            background: transparent;
            color: var(--text-soft);
            border-color: var(--border);
        }
        .btn-ghost:hover {
            background: #F7F9FC;
            border-color: var(--rust);
            color: var(--rust);
        }
        .btn-sm { padding: 7px 14px; font-size: 12.5px; }
        .btn-lg { padding: 13px 24px; font-size: 15px; }
        .btn-block { width: 100%; justify-content: center; }

        /* ═══════════════════════════════════════════════════════════
           FLASH
        ═══════════════════════════════════════════════════════════ */
        .flash-container {
            max-width: 1200px;
            margin: 20px auto 0;
            padding: 0 var(--container-padding);
        }
        .flash-message {
            padding: 13px 18px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid;
            animation: flashIn .3s ease;
        }
        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .flash-success { background: #E8F5E9; color: #1E7A47; border-color: #C8E6C9; }
        .flash-error   { background: #FFEBEE; color: #C62828; border-color: #FFCDD2; }
        .flash-info    { background: #E3F2FD; color: #0D47A1; border-color: #BBDEFB; }

        /* ═══════════════════════════════════════════════════════════
           BACK TO TOP
        ═══════════════════════════════════════════════════════════ */
        .back-to-top {
            position: fixed;
            bottom: 90px;
            right: 24px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--rust);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(181, 80, 42, .35);
            transition: all .25s ease;
            z-index: 90;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            border: none;
        }
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .back-to-top:hover {
            background: #9A4523;
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(181, 80, 42, .45);
        }

        /* ═══════════════════════════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════════════════════════ */
        .pub-footer {
            background: var(--ink);
            color: #fff;
            padding: 56px 0 20px;
            margin-top: 60px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            text-decoration: none;
        }
        .footer-brand .brand-mark {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            color: #fff;
            font-weight: 800;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .footer-brand .brand-name {
            font-weight: 700;
            font-size: 17px;
            color: #fff;
        }
        .footer-brand .brand-name span { color: var(--rust); }

        .footer-description {
            font-size: 13px;
            color: #8A91A0;
            line-height: 1.7;
            max-width: 300px;
        }

        .footer-grid h4 {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #B9BFCC;
            margin-bottom: 16px;
        }

        .footer-grid ul { list-style: none; padding: 0; margin: 0; }
        .footer-grid ul li { margin-bottom: 10px; }
        .footer-grid ul li a {
            color: #8A91A0;
            text-decoration: none;
            font-size: 13px;
            transition: color .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .footer-grid ul li a:hover { color: #fff; }
        .footer-grid ul li a i { font-size: 13px; width: 16px; text-align: center; }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, .06);
            padding-top: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            color: #6A7280;
            flex-wrap: wrap;
            gap: 10px;
        }
        .footer-bottom a { color: #8A91A0; text-decoration: none; }
        .footer-bottom a:hover { color: #fff; }

        /* ═══════════════════════════════════════════════════════════
           BOTTOM NAV (mobile)
        ═══════════════════════════════════════════════════════════ */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #fff;
            border-top: 1px solid var(--border);
            z-index: 1001;
            padding: 6px 0 env(safe-area-inset-bottom, 8px) 0;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, .05);
            height: 68px;
        }

        .bottom-nav__inner {
            display: flex;
            align-items: center;
            justify-content: space-around;
            max-width: 600px;
            margin: 0 auto;
            padding: 0 8px;
            height: 100%;
        }

        .bottom-nav__item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            text-decoration: none;
            color: var(--muted);
            font-size: 10px;
            font-weight: 600;
            transition: color .2s;
            padding: 4px 12px;
            border-radius: 10px;
            min-width: 56px;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .bottom-nav__item i {
            font-size: 20px;
            transition: transform .2s;
        }
        .bottom-nav__item span {
            font-size: 9.5px;
            line-height: 1.2;
        }
        .bottom-nav__item.active { color: var(--rust); }
        .bottom-nav__item.active i { transform: scale(1.08); }

        .bottom-nav__badge {
            position: absolute;
            top: 0;
            right: 2px;
            background: #C62828;
            color: #fff;
            font-size: 9px;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 999px;
            min-width: 17px;
            text-align: center;
            border: 2px solid #fff;
            line-height: 1.3;
        }

        /* Bouton central Publier */
        .bottom-nav__item.publier {
            background: linear-gradient(135deg, var(--rust), #d4754a);
            color: #fff;
            border-radius: 999px;
            padding: 8px 18px;
            min-width: 74px;
            box-shadow: 0 6px 16px rgba(181, 80, 42, .4);
            margin-top: -14px;
            transition: all .2s;
        }
        .bottom-nav__item.publier i { font-size: 16px; }
        .bottom-nav__item.publier span { font-size: 10px; font-weight: 700; }
        .bottom-nav__item.publier:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(181, 80, 42, .5);
        }
        .bottom-nav__item.publier:active { transform: scale(.95); }

        /* ═══════════════════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .pub-links { gap: 16px; }
            .search-header { max-width: 360px; }
        }

        @media (max-width: 900px) {
            .pub-links,
            .pub-actions .btn-ghost { display: none; }
            .pub-burger { display: block; }
            .mobile-icons { display: flex; }
            .search-header { max-width: 280px; }
            .pub-mobile-menu { display: block; }
        }

        @media (max-width: 600px) {
            :root { --header-height: 64px; }

            .pub-nav { padding: 10px var(--container-padding); gap: 8px; }

            .search-header { display: none; }
            .search-toggle-btn { display: flex; }
            .pub-actions { display: none; }

            .brand-mark { width: 34px; height: 34px; font-size: 16px; }
            .brand-name { font-size: 16px; }

            #headerAutocomplete { max-height: 320px; }

            .search-overlay .search-modal { padding: 18px 18px 22px; }
        }

        @media (max-width: 480px) {
            .bottom-nav__item { min-width: 46px; padding: 3px 6px; }
            .bottom-nav__item i { font-size: 18px; }
            .bottom-nav__item span { font-size: 9px; }
            .bottom-nav__item.publier { padding: 6px 14px; min-width: 62px; margin-top: -10px; }

            .back-to-top {
                bottom: 82px;
                right: 16px;
                width: 42px;
                height: 42px;
                font-size: 14px;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }
            .footer-grid > div:first-child { grid-column: 1 / -1; }
            .footer-description { max-width: 100%; }

            .footer-bottom { flex-direction: column; text-align: center; }
        }

        @media (max-width: 380px) {
            .bottom-nav__item { min-width: 40px; padding: 2px 4px; }
            .bottom-nav__item i { font-size: 16px; }
            .bottom-nav__item span { font-size: 8px; }
            .bottom-nav__item.publier { padding: 5px 10px; min-width: 52px; margin-top: -8px; }
            .bottom-nav__item.publier span { display: none; }
        }

        @media (max-width: 768px) {
            input, select, textarea { font-size: 16px !important; }
        }

        @media (max-width: 900px) {
            body { padding-bottom: 76px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div id="app">

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <header class="pub-header">
        <nav class="pub-nav">

            {{-- Brand --}}
            <a href="{{ route('home') }}" class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span>Immo</span></div>
            </a>

            {{-- Recherche desktop --}}
            <div class="search-header">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input
                        type="text"
                        id="headerSearch"
                        placeholder="Rechercher un besoin, un bien, une agence..."
                        autocomplete="off"
                        aria-label="Recherche"
                        inputmode="search"
                    >
                    <span class="search-shortcut">⌘K</span>
                    <div id="headerAutocomplete"></div>
                </div>
            </div>

            {{-- Liens desktop --}}
            <div class="pub-links">
                <a href="{{ route('besoins.index') }}">Besoins</a>
                <a href="{{ route('biens.index') }}">Biens</a>
                <a href="{{ route('home') }}#comment">Comment ça marche</a>
                <a href="{{ route('register.agence') }}">Pour les agences</a>
            </div>

            {{-- Actions desktop --}}
            <div class="pub-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-user"></i>
                        {{ Auth::user()->prenom ?? 'Profil' }}
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

            {{-- Icônes mobile --}}
            <div class="mobile-icons">
                <button class="search-toggle-btn" id="searchToggleBtn" onclick="openSearch()" aria-label="Rechercher">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <button class="pub-burger" id="pubBurger" onclick="toggleMobileMenu()" aria-label="Menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </nav>

        {{-- Menu mobile --}}
        <div class="pub-mobile-menu" id="pubMobileMenu">
            <a href="{{ route('besoins.index') }}" class="menu-item">
                <span class="menu-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <span class="menu-text">Parcourir les besoins</span>
            </a>
            <a href="{{ route('biens.index') }}" class="menu-item">
                <span class="menu-icon"><i class="fa-solid fa-house"></i></span>
                <span class="menu-text">Parcourir les biens</span>
            </a>
            <a href="{{ route('home') }}#comment" class="menu-item">
                <span class="menu-icon"><i class="fa-solid fa-question-circle"></i></span>
                <span class="menu-text">Comment ça marche</span>
            </a>
            <a href="{{ route('register.agence') }}" class="menu-item">
                <span class="menu-icon"><i class="fa-solid fa-building"></i></span>
                <span class="menu-text">Pour les agences</span>
            </a>

            <div class="menu-divider"></div>

            @auth
                <a href="{{ route('dashboard') }}" class="menu-item">
                    <span class="menu-icon"><i class="fa-solid fa-gauge-high"></i></span>
                    <span class="menu-text">Tableau de bord</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="menu-item">
                        <span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                        <span class="menu-text">Déconnexion</span>
                    </button>
                </form>
            @else
                <div class="menu-auth">
                    <a href="{{ route('login') }}" class="menu-item">
                        <span class="menu-text">Se connecter</span>
                    </a>
                    <a href="{{ route('register') }}" class="menu-item">
                        <span class="menu-text">S'inscrire</span>
                    </a>
                </div>
            @endauth
        </div>

        {{-- Overlay recherche mobile --}}
        <div class="search-overlay" id="searchOverlay" onclick="closeSearchOutside(event)">
            <div class="search-modal" onclick="event.stopPropagation()">
                <button class="search-close" onclick="closeSearch()" aria-label="Fermer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input
                        type="text"
                        id="mobileSearchInput"
                        placeholder="Rechercher..."
                        autocomplete="off"
                        aria-label="Recherche"
                        inputmode="search"
                    >
                </div>
                <div class="search-suggestions">
                    <div class="suggestion-title">Recherches populaires</div>
                    <a href="{{ route('recherche', ['q' => 'appartement']) }}" class="suggestion-item">
                        <i class="fa-solid fa-building"></i> Appartement
                    </a>
                    <a href="{{ route('recherche', ['q' => 'studio']) }}" class="suggestion-item">
                        <i class="fa-solid fa-house"></i> Studio
                    </a>
                    <a href="{{ route('recherche', ['q' => 'villa']) }}" class="suggestion-item">
                        <i class="fa-solid fa-house-chimney"></i> Villa
                    </a>
                    <a href="{{ route('recherche', ['q' => 'bureau']) }}" class="suggestion-item">
                        <i class="fa-solid fa-briefcase"></i> Bureau
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         FLASH
    ═══════════════════════════════════════════ --}}
    @if(session('success') || session('error') || session('info'))
        <div class="flash-container">
            @if(session('success'))
                <div class="flash-message flash-success">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash-message flash-error">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="flash-message flash-info">
                    <i class="fa-solid fa-circle-info"></i> {{ session('info') }}
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         MAIN
    ═══════════════════════════════════════════ --}}
    <main>
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════ --}}
    <footer class="pub-footer">
        <div class="wrap">
            <div class="footer-grid">
                <div>
                    <a href="{{ route('home') }}" class="footer-brand">
                        <div class="brand-mark">D</div>
                        <div class="brand-name">Doya<span>Immo</span></div>
                    </a>
                    <p class="footer-description">
                        La plateforme de mise en relation immobilière entre clients et agences à Dakar.
                    </p>
                </div>

                <div>
                    <h4>Plateforme</h4>
                    <ul>
                        <li><a href="{{ route('besoins.index') }}">Parcourir les besoins</a></li>
                        <li><a href="{{ route('register.particulier') }}">Créer un compte</a></li>
                        <li><a href="{{ route('register.agence') }}">Devenir agence</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Ressources</h4>
                    <ul>
                        <li><a href="{{ route('cgu') }}">Conditions d'utilisation</a></li>
                        <li><a href="{{ route('mentions-legales') }}">Mentions légales</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Contact</h4>
                    <ul>
                        <li>
                            <a href="mailto:contact@doyaimmo.sn">
                                <i class="fa-solid fa-envelope"></i> contact@doyaimmo.sn
                            </a>
                        </li>
                        <li>
                            <a href="tel:+221774612082">
                                <i class="fa-solid fa-phone"></i> +221 77 461 20 82
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/doyaimmo" target="_blank" rel="noopener">
                                <i class="fa-brands fa-facebook"></i> Facebook
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/doyaimmo" target="_blank" rel="noopener">
                                <i class="fa-brands fa-instagram"></i> Instagram
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} DoyaImmo. Tous droits réservés.</span>
            </div>
        </div>
    </footer>

    {{-- ═══════════════════════════════════════════
         BOTTOM NAV (mobile)
    ═══════════════════════════════════════════ --}}
    <nav class="bottom-nav" role="navigation" aria-label="Navigation principale">
        <div class="bottom-nav__inner">

            <a href="{{ route('home') }}"
               class="bottom-nav__item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Accueil</span>
            </a>

            <a href="{{ route('biens.index') }}"
               class="bottom-nav__item {{ request()->routeIs('biens.*') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i>
                <span>Biens</span>
                @if(isset($nbBiens) && $nbBiens > 0)
                    <span class="bottom-nav__badge">{{ $nbBiens }}</span>
                @endif
            </a>

            @auth
                @if(Auth::user()->isAgence())
                    <a href="{{ route('agence.biens.create') }}" class="bottom-nav__item publier">
                        <i class="fa-solid fa-plus"></i>
                        <span>Publier</span>
                    </a>
                @elseif(Auth::user()->isParticulier())
                    <a href="{{ route('particulier.demandes.create') }}" class="bottom-nav__item publier">
                        <i class="fa-solid fa-plus"></i>
                        <span>Publier</span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bottom-nav__item publier">
                        <i class="fa-solid fa-plus"></i>
                        <span>Publier</span>
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}" class="bottom-nav__item publier">
                    <i class="fa-solid fa-plus"></i>
                    <span>Publier</span>
                </a>
            @endauth

            <a href="{{ route('besoins.index') }}"
               class="bottom-nav__item {{ request()->routeIs('besoins.*') ? 'active' : '' }}">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Besoins</span>
                @if(isset($nbBesoins) && $nbBesoins > 0)
                    <span class="bottom-nav__badge">{{ $nbBesoins }}</span>
                @endif
            </a>

            <a href="{{ route('agences.public.index') }}"
               class="bottom-nav__item {{ request()->routeIs('agences.*') ? 'active' : '' }}">
                <i class="fa-solid fa-building-columns"></i>
                <span>Agences</span>
            </a>
        </div>
    </nav>

    {{-- Back to top --}}
    <button class="back-to-top" id="backToTop" onclick="scrollToTop()" aria-label="Retour en haut">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
</div>


<script>
/* ═══════════════════════════════════════════════════════════
   ANTI-FLASH
═══════════════════════════════════════════════════════════ */
(function () {
    function showContent() {
        const app = document.getElementById('app');
        if (app) app.classList.add('ready');
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(showContent, 30);
    } else {
        document.addEventListener('DOMContentLoaded', () => setTimeout(showContent, 30));
    }

    window.addEventListener('load', () => setTimeout(showContent, 50));
    setTimeout(showContent, 400);
})();

/* ═══════════════════════════════════════════════════════════
   MENU MOBILE
═══════════════════════════════════════════════════════════ */
function toggleMobileMenu() {
    const menu = document.getElementById('pubMobileMenu');
    const burger = document.getElementById('pubBurger');
    const isOpen = menu.classList.toggle('open');
    burger.classList.toggle('active');
    burger.setAttribute('aria-expanded', isOpen);
}

function closeMobileMenu() {
    const menu = document.getElementById('pubMobileMenu');
    const burger = document.getElementById('pubBurger');
    menu.classList.remove('open');
    burger.classList.remove('active');
    burger.setAttribute('aria-expanded', 'false');
}

document.querySelectorAll('#pubMobileMenu .menu-item').forEach(link => {
    link.addEventListener('click', closeMobileMenu);
});

document.addEventListener('click', e => {
    const menu = document.getElementById('pubMobileMenu');
    if (menu.classList.contains('open') &&
        !e.target.closest('.pub-nav') &&
        !e.target.closest('.pub-mobile-menu')) {
        closeMobileMenu();
    }
});

/* ═══════════════════════════════════════════════════════════
   RECHERCHE MOBILE
═══════════════════════════════════════════════════════════ */
function openSearch() {
    const overlay = document.getElementById('searchOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
        document.getElementById('mobileSearchInput')?.focus();
    }, 250);
}

function closeSearch() {
    const overlay = document.getElementById('searchOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function closeSearchOutside(event) {
    if (event.target === event.currentTarget) closeSearch();
}

document.addEventListener('DOMContentLoaded', () => {
    const mobileInput = document.getElementById('mobileSearchInput');
    if (mobileInput) {
        mobileInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const q = this.value.trim();
                if (q.length > 0) window.location.href = `/recherche?q=${encodeURIComponent(q)}`;
            }
        });
    }
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeSearch();
        closeMobileMenu();
    }
});

/* ═══════════════════════════════════════════════════════════
   RECHERCHE HEADER (desktop)
═══════════════════════════════════════════════════════════ */
function performSearch() {
    const q = document.getElementById('headerSearch')?.value.trim();
    if (q && q.length > 0) window.location.href = `/recherche?q=${encodeURIComponent(q)}`;
}

document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('headerSearch');
    const results = document.getElementById('headerAutocomplete');
    if (!search || !results) return;

    let debounceTimer;

    // Raccourci ⌘K / Ctrl+K
    document.addEventListener('keydown', e => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            search.focus();
            search.select();
        }
    });

    search.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    search.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();

        if (q.length < 2) {
            results.classList.remove('is-open');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/recherche/autocomplete?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        results.innerHTML = `
                            <div class="autocomplete-empty">
                                <i class="fa-regular fa-face-frown"></i>
                                Aucun résultat pour "<strong>${q}</strong>"
                            </div>`;
                        results.classList.add('is-open');
                        return;
                    }
                    renderResults(data);
                })
                .catch(() => results.classList.remove('is-open'));
        }, 250);
    });

    function renderResults(data) {
        const typeIcons = {
            besoin:   'fa-solid fa-magnifying-glass',
            bien:     'fa-solid fa-building',
            agence:   'fa-solid fa-building-columns',
            quartier: 'fa-solid fa-location-dot'
        };

        results.innerHTML = data.map(item => {
            const icon = typeIcons[item.type] || 'fa-solid fa-circle';
            return `
                <div class="autocomplete-item" onclick="window.location.href='${item.url || '#'}'">
                    <div class="item-icon ${item.type || 'besoin'}">
                        <i class="${icon}"></i>
                    </div>
                    <div class="item-content">
                        <div class="item-title">${item.label}</div>
                        <div class="item-desc">${item.description || ''}</div>
                    </div>
                    <span class="item-type">${item.type || 'Résultat'}</span>
                </div>`;
        }).join('');

        results.classList.add('is-open');
    }

    document.addEventListener('click', e => {
        if (!e.target.closest('.search-input-wrapper')) {
            results.classList.remove('is-open');
        }
    });
});

/* ═══════════════════════════════════════════════════════════
   BACK TO TOP
═══════════════════════════════════════════════════════════ */
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

let backToTopTicking = false;
document.addEventListener('scroll', () => {
    if (backToTopTicking) return;
    backToTopTicking = true;

    requestAnimationFrame(() => {
        const btn = document.getElementById('backToTop');
        if (btn) {
            btn.classList.toggle('show', window.scrollY > 400);
        }
        backToTopTicking = false;
    });
}, { passive: true });
</script>

@stack('scripts')
@stack('jsonld')
</body>
</html>