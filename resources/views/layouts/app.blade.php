<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ============================================
         ✅ SEO : META TAGS
         ============================================ -->
    <title>@yield('title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')</title>
    <meta name="description" content="@yield('meta_description', 'DoyaImmo est une plateforme immobilière basée à Dakar qui met en relation particuliers et agences immobilières pour la recherche, la location et la vente de logements.')">
    <link rel="canonical" href="@yield('canonical', 'https://doyaimmo.com'.request()->getRequestUri())">
    <meta name="robots" content="@yield('robots', 'index, follow')">

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Open Graph -->
    <meta property="og:site_name" content="DoyaImmo">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')">
    <meta property="og:description" content="@yield('og_description', 'DoyaImmo met en relation particuliers et agences immobilières à Dakar. Publiez votre recherche ou consultez les biens disponibles, quartier par quartier.')">
    <meta property="og:url" content="@yield('canonical', 'https://doyaimmo.com'.request()->getRequestUri())">
    <meta property="og:image" content="{{ asset('favicon.png') }}">
    <meta property="og:locale" content="fr_SN">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')">
    <meta name="twitter:description" content="@yield('og_description', 'DoyaImmo met en relation particuliers et agences immobilières à Dakar. Publiez votre recherche ou consultez les biens disponibles, quartier par quartier.')">
    <meta name="twitter:image" content="{{ asset('favicon.png') }}">
    <!-- ========================================== -->

    <!-- ===== STYLES CRITIQUES ANTI-FLASH ===== -->
    <style>
        /* ✅ Anti-flash : cacher tout le contenu au chargement */
        #app {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        #app.ready {
            opacity: 1;
        }
        
        /* ✅ Empêcher le flash de la bottom nav */
        .bottom-nav {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .bottom-nav.visible {
            opacity: 1;
        }
        
        /* ✅ Fond de la page */
        html, body {
            background: #F7F9FC;
            min-height: 100vh;
        }
        
        /* ✅ Contenu principal masqué tant que les styles ne sont pas chargés */
        main {
            visibility: hidden;
        }
        main.visible {
            visibility: visible;
        }
    </style>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"></noscript>

    <!-- Font Awesome (chargement non-bloquant) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- ============================================
         STYLES
         ============================================ -->
    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            --gold-soft: rgba(212, 175, 55, 0.12);
            --green: #1E7A47;
            --green-soft: rgba(30, 122, 71, 0.12);
            --radius: 16px;
            --display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --header-height: 70px;
            --container-padding: clamp(16px, 4vw, 24px);
        }

        body {
            font-family: var(--display);
            background: #F7F9FC;
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        img, svg, video {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--container-padding);
            width: 100%;
        }

        .section {
            padding: clamp(40px, 8vw, 60px) 0;
        }

        /* ============================================
           HEADER
           ============================================ */
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
            padding: 10px var(--container-padding);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: clamp(8px, 2vw, 16px);
            min-height: var(--header-height);
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--ink);
            flex-shrink: 0;
        }

        .brand-mark {
            width: clamp(32px, 5vw, 36px);
            height: clamp(32px, 5vw, 36px);
            border-radius: 10px;
            background: var(--rust);
            color: #fff;
            font-family: var(--display);
            font-weight: 800;
            font-size: clamp(16px, 2.5vw, 18px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-name {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(16px, 2vw, 18px);
        }

        .brand-name span {
            color: var(--rust);
        }

        /* ============================================
           SEARCH
           ============================================ */
        .search-header {
            flex: 1;
            max-width: 450px;
            min-width: 120px;
            width: 100%;
        }

        .search-input-wrapper {
            position: relative;
            width: 100%;
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 15px;
            pointer-events: none;
            z-index: 2;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 10px 14px 10px 46px;
            border: 2px solid var(--border);
            border-radius: 50px;
            font-size: clamp(14px, 1.2vw, 16px);
            background: #F7F9FC;
            transition: all 0.3s ease;
            font-family: var(--display);
            color: var(--ink);
            -webkit-appearance: none;
            appearance: none;
        }

        .search-input-wrapper input::placeholder {
            color: var(--muted);
            font-size: clamp(12px, 1vw, 14px);
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--rust);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(181, 80, 42, 0.08);
        }

        .search-shortcut {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: var(--muted);
            background: var(--border);
            padding: 2px 10px;
            border-radius: 4px;
            font-weight: 600;
            pointer-events: none;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            display: none;
        }

        @media (min-width: 769px) {
            .search-shortcut {
                display: block;
            }
        }

        /* ============================================
           NAV LINKS - UNIQUEMENT SUR DESKTOP
           ============================================ */
        .pub-links {
            display: flex;
            gap: clamp(16px, 2.5vw, 24px);
            align-items: center;
        }

        .pub-links a {
            color: var(--text-soft);
            text-decoration: none;
            font-size: clamp(12px, 1.1vw, 14px);
            transition: color 0.2s;
            white-space: nowrap;
            position: relative;
        }

        .pub-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--rust);
            transition: width 0.3s;
        }

        .pub-links a:hover::after {
            width: 100%;
        }

        .pub-links a:hover {
            color: var(--rust);
        }

        .pub-actions {
            display: flex;
            gap: clamp(8px, 1.5vw, 12px);
            align-items: center;
            flex-shrink: 0;
        }

        .pub-burger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            flex-shrink: 0;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .pub-burger:hover {
            background: var(--border);
        }

        .pub-burger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--ink);
            margin: 4px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .pub-burger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .pub-burger.active span:nth-child(2) {
            opacity: 0;
        }

        .pub-burger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        /* ============================================
           MOBILE MENU (hamburger)
           ============================================ */
        .pub-mobile-menu {
            display: none;
            background: #fff;
            border-top: 1px solid var(--border);
            width: 100%;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .pub-mobile-menu.open {
            display: block;
            max-height: 600px;
            padding: 16px var(--container-padding) 24px;
            overflow-y: auto;
        }

        .pub-mobile-menu .menu-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            color: var(--text-soft);
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.2s;
            border: none;
            background: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
            font-family: var(--display);
        }

        .pub-mobile-menu .menu-item:hover {
            background: var(--rust-soft);
            color: var(--rust);
        }

        .pub-mobile-menu .menu-item .menu-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--muted);
            flex-shrink: 0;
            transition: color 0.2s;
        }

        .pub-mobile-menu .menu-item:hover .menu-icon {
            color: var(--rust);
        }

        .pub-mobile-menu .menu-item .menu-text {
            flex: 1;
        }

        .pub-mobile-menu .menu-item .menu-arrow {
            color: var(--muted);
            font-size: 14px;
            transition: transform 0.2s;
        }

        .pub-mobile-menu .menu-item:hover .menu-arrow {
            transform: translateX(4px);
            color: var(--rust);
        }

        .pub-mobile-menu .menu-divider {
            height: 1px;
            background: var(--border);
            margin: 8px 0;
        }

        .pub-mobile-menu .menu-auth {
            margin-top: 8px;
        }

        .pub-mobile-menu .menu-auth .menu-item {
            background: var(--rust);
            color: #fff;
            justify-content: center;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
        }

        .pub-mobile-menu .menu-auth .menu-item:hover {
            background: #9A4523;
            color: #fff;
        }

        .pub-mobile-menu .menu-auth .menu-item .menu-icon {
            color: #fff;
        }

        .pub-mobile-menu .menu-auth .menu-item:hover .menu-icon {
            color: #fff;
        }

        /* ============================================
           BOUTON RECHERCHE MOBILE
           ============================================ */
        .mobile-icons {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .search-toggle-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            color: var(--text-soft);
            font-size: 18px;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .search-toggle-btn:hover {
            background: var(--border);
            color: var(--rust);
        }

        /* ============================================
           OVERLAY RECHERCHE MOBILE
           ============================================ */
        .search-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            animation: fadeIn 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .search-overlay.active {
            display: block;
        }

        .search-overlay .search-modal {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 20px 24px 24px;
            border-bottom: 1px solid var(--border);
            animation: slideDown 0.3s ease;
        }

        .search-overlay .search-modal .search-close {
            position: absolute;
            right: 16px;
            top: 16px;
            background: none;
            border: none;
            font-size: 22px;
            color: var(--muted);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .search-overlay .search-modal .search-close:hover {
            background: var(--border);
            color: var(--ink);
        }

        .search-overlay .search-modal .search-input-wrapper {
            position: relative;
            margin-top: 8px;
        }

        .search-overlay .search-modal .search-input-wrapper .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 18px;
            pointer-events: none;
            z-index: 2;
        }

        .search-overlay .search-modal .search-input-wrapper input {
            width: 100%;
            padding: 14px 20px 14px 52px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 18px;
            font-family: var(--display);
            background: #F7F9FC;
            color: var(--ink);
            transition: all 0.3s ease;
            -webkit-appearance: none;
            appearance: none;
        }

        .search-overlay .search-modal .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--rust);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(181, 80, 42, 0.08);
        }

        .search-overlay .search-modal .search-input-wrapper input::placeholder {
            color: var(--muted);
            font-size: 16px;
        }

        .search-overlay .search-modal .search-suggestions {
            margin-top: 16px;
        }

        .search-overlay .search-modal .search-suggestions .suggestion-title {
            font-size: 13px;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .search-overlay .search-modal .search-suggestions .suggestion-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text-soft);
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
        }

        .search-overlay .search-modal .search-suggestions .suggestion-item:hover {
            background: var(--border);
        }

        .search-overlay .search-modal .search-suggestions .suggestion-item i {
            color: var(--muted);
            width: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: clamp(12px, 1vw, 14px);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: var(--display);
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

        .btn-sm {
            padding: 6px 14px;
            font-size: clamp(11px, 0.9vw, 13px);
        }

        .btn-lg {
            padding: clamp(12px, 1.5vw, 14px) clamp(20px, 2.5vw, 28px);
            font-size: clamp(14px, 1.2vw, 16px);
        }

        .btn-block {
            width: 100%;
            justify-content: center;
        }

        /* ============================================
           FLASH MESSAGES
           ============================================ */
        .flash-message {
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: clamp(13px, 1vw, 14px);
            display: flex;
            align-items: center;
            gap: 8px;
            word-break: break-word;
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

        /* ============================================
           AUTOCOMPLETE
           ============================================ */
        #headerAutocomplete {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 12px 48px rgba(0,0,0,0.12);
            z-index: 1000;
            display: none;
            max-height: 420px;
            overflow-y: auto;
            padding: 8px 0;
        }

        #headerAutocomplete .autocomplete-item {
            padding: clamp(10px, 1vw, 12px) clamp(14px, 1.5vw, 18px);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 14px;
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
            width: clamp(30px, 3vw, 36px);
            height: clamp(30px, 3vw, 36px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(12px, 1vw, 14px);
            color: #fff;
            flex-shrink: 0;
        }

        #headerAutocomplete .autocomplete-item .item-icon.besoin {
            background: #E3F2FD;
            color: #0D47A1;
        }

        #headerAutocomplete .autocomplete-item .item-icon.bien {
            background: #E8F5E9;
            color: #1E7A47;
        }

        #headerAutocomplete .autocomplete-item .item-icon.agence {
            background: #FFF8E1;
            color: #E65100;
        }

        #headerAutocomplete .autocomplete-item .item-icon.quartier {
            background: #F3E5F5;
            color: #4A148C;
        }

        #headerAutocomplete .autocomplete-item .item-content {
            flex: 1;
            min-width: 0;
        }

        #headerAutocomplete .autocomplete-item .item-title {
            font-weight: 600;
            font-size: clamp(13px, 1vw, 14px);
            color: var(--ink);
            word-break: break-word;
        }

        #headerAutocomplete .autocomplete-item .item-desc {
            font-size: clamp(11px, 0.8vw, 12px);
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #headerAutocomplete .autocomplete-item .item-type {
            font-size: clamp(9px, 0.7vw, 10px);
            text-transform: uppercase;
            color: var(--muted);
            background: var(--border);
            padding: 2px 12px;
            border-radius: 999px;
            flex-shrink: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        #headerAutocomplete .autocomplete-empty {
            padding: 30px 20px;
            text-align: center;
            color: var(--muted);
            font-size: clamp(13px, 1vw, 14px);
        }

        #headerAutocomplete .autocomplete-empty i {
            font-size: 32px;
            display: block;
            margin-bottom: 10px;
            opacity: 0.3;
        }

        /* ============================================
           FLÈCHE RETOUR EN HAUT
           ============================================ */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--rust);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(181, 80, 42, 0.4);
            transition: all 0.3s ease;
            z-index: 999;
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
            box-shadow: 0 6px 28px rgba(181, 80, 42, 0.5);
        }

        .back-to-top:active {
            transform: scale(0.95);
        }

        /* ============================================
           FOOTER
           ============================================ */
        .pub-footer {
            background: var(--ink);
            color: #fff;
            padding: clamp(40px, 5vw, 48px) 0 clamp(16px, 2vw, 24px);
            margin-top: 60px;
            width: 100%;
            position: relative;
        }

        .footer-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--container-padding);
            width: 100%;
            position: relative;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: clamp(24px, 4vw, 40px);
            margin-bottom: 40px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            text-decoration: none;
        }

        .footer-brand .brand-mark {
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

        .footer-brand .brand-name {
            font-family: var(--display);
            font-weight: 700;
            font-size: 18px;
            color: #fff;
        }

        .footer-brand .brand-name span {
            color: var(--rust);
        }

        .footer-description {
            font-size: clamp(12px, 0.8vw, 13px);
            color: #8A91A0;
            line-height: 1.7;
            max-width: 280px;
        }

        .footer-grid h4 {
            font-size: clamp(12px, 0.8vw, 13px);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #B9BFCC;
            margin-bottom: 14px;
        }

        .footer-grid ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-grid ul li {
            margin-bottom: 8px;
        }

        .footer-grid ul li a {
            color: #8A91A0;
            text-decoration: none;
            font-size: clamp(12px, 0.8vw, 13px);
            transition: color 0.2s;
            display: inline-block;
        }

        .footer-grid ul li a:hover {
            color: #fff;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: clamp(11px, 0.7vw, 12.5px);
            color: #6A7280;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ============================================
           RESPONSIVE HEADER
           ============================================ */
        @media (max-width: 1024px) {
            .pub-links {
                gap: 14px;
            }
            .search-header {
                max-width: 320px;
            }
        }

        @media (max-width: 820px) {
            .pub-links,
            .pub-actions .btn-ghost {
                display: none;
            }
            .pub-burger {
                display: block;
            }
            .search-header {
                max-width: 280px;
                min-width: 100px;
            }
        }

        @media (max-width: 600px) {
            .pub-nav {
                padding: 8px var(--container-padding);
                gap: 8px;
            }

            .search-header {
                display: none !important;
            }

            .search-toggle-btn {
                display: flex;
            }

            .pub-actions {
                display: none;
            }

            .brand-name {
                font-size: 15px;
            }

            .brand-mark {
                width: 30px;
                height: 30px;
                font-size: 15px;
            }

            #headerAutocomplete {
                max-height: 300px;
            }

            #headerAutocomplete .autocomplete-item {
                padding: 10px 14px;
                gap: 10px;
            }

            #headerAutocomplete .autocomplete-item .item-icon {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .pub-mobile-menu.open {
                max-height: 80vh;
                padding: 12px 16px 20px;
            }

            .pub-mobile-menu .menu-item {
                padding: 12px 14px;
                font-size: 15px;
            }

            .search-overlay .search-modal {
                padding: 16px 20px 20px;
            }

            .search-overlay .search-modal .search-input-wrapper input {
                font-size: 16px;
                padding: 12px 16px 12px 48px;
            }

            .search-overlay .search-modal .search-input-wrapper input::placeholder {
                font-size: 15px;
            }
        }

        @media (max-width: 400px) {
            .pub-nav {
                padding: 6px 12px;
            }
            .brand-name {
                font-size: 13px;
            }
            .brand-mark {
                width: 26px;
                height: 26px;
                font-size: 13px;
                border-radius: 8px;
            }
            .search-toggle-btn {
                font-size: 16px;
                padding: 6px;
            }
            .search-overlay .search-modal {
                padding: 12px 16px 16px;
            }
            .search-overlay .search-modal .search-input-wrapper input {
                font-size: 15px;
                padding: 10px 14px 10px 44px;
            }
            .search-overlay .search-modal .search-input-wrapper .search-icon {
                left: 12px;
                font-size: 15px;
            }

            .pub-mobile-menu .menu-item {
                padding: 10px 12px;
                font-size: 14px;
                gap: 10px;
            }

            .pub-mobile-menu .menu-item .menu-icon {
                width: 20px;
                font-size: 15px;
            }
        }

        /* ============================================
           CORRECTION ZOOM SUR MOBILE
           ============================================ */
        @media (max-width: 768px) {
            input,
            select,
            textarea {
                font-size: 16px !important;
            }
        }

        /* ============================================
           RESPONSIVE FOOTER
           ============================================ */
        @media (max-width: 820px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }

            .footer-description {
                max-width: 100%;
            }
        }

        @media (max-width: 640px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }

            .footer-grid:first-child {
                grid-column: 1 / -1;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
                padding-top: 16px;
            }

            .footer-description {
                max-width: 100%;
            }

            .back-to-top {
                bottom: 20px;
                right: 20px;
                width: 42px;
                height: 42px;
                font-size: 17px;
            }
        }

        @media (max-width: 420px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .footer-brand .brand-mark {
                width: 30px;
                height: 30px;
                font-size: 15px;
            }

            .footer-brand .brand-name {
                font-size: 16px;
            }

            .back-to-top {
                bottom: 16px;
                right: 16px;
                width: 38px;
                height: 38px;
                font-size: 15px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ============================================
           CACHE SUR MOBILE
           ============================================ */
        @media (max-width: 600px) {
            .hide-mobile {
                display: none !important;
            }
        }

        /* ============================================
           BOTTOM NAVIGATION (MENU MOBILE)
           ============================================ */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid var(--border);
            z-index: 1000;
            padding: 6px 0 env(safe-area-inset-bottom, 8px) 0;
            box-shadow: 0 -2px 16px rgba(0,0,0,0.06);
            height: 68px;
        }

        .bottom-nav .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-around;
            max-width: 600px;
            margin: 0 auto;
            padding: 0 8px;
            height: 100%;
        }

        .bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            text-decoration: none;
            color: var(--muted);
            font-size: 10px;
            font-weight: 500;
            transition: color 0.2s;
            padding: 4px 12px;
            border-radius: 8px;
            min-width: 56px;
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            font-family: var(--display);
        }

        .bottom-nav .nav-item i {
            font-size: 22px;
            transition: all 0.2s;
        }

        .bottom-nav .nav-item span {
            font-size: 9px;
            line-height: 1.2;
        }

        .bottom-nav .nav-item.active {
            color: var(--rust);
        }

        .bottom-nav .nav-item.active i {
            transform: scale(1.05);
        }

        .bottom-nav .nav-item .badge-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #C62828;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .bottom-nav .nav-item .badge-count {
            position: absolute;
            top: 0;
            right: -4px;
            background: #C62828;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
            border: 2px solid #fff;
            line-height: 1.4;
        }

        /* Bouton Publier (style différent - au centre) */
        .bottom-nav .nav-item.publier {
            background: var(--rust);
            color: #fff;
            border-radius: 50px;
            padding: 6px 16px;
            min-width: 70px;
            box-shadow: 0 4px 12px rgba(181, 80, 42, 0.35);
            transition: all 0.2s;
            margin-top: -10px;
        }

        .bottom-nav .nav-item.publier i {
            font-size: 18px;
        }

        .bottom-nav .nav-item.publier span {
            font-size: 10px;
            font-weight: 600;
        }

        .bottom-nav .nav-item.publier:hover {
            background: #9A4523;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(181, 80, 42, 0.45);
        }

        .bottom-nav .nav-item.publier:active {
            transform: scale(0.95);
        }

        /* Afficher sur mobile seulement */
        @media (max-width: 820px) {
            .bottom-nav {
                display: block;
                opacity: 1;
            }

            /* Ajouter un padding en bas sur le body pour ne pas cacher le contenu */
            body {
                padding-bottom: 76px;
            }

            /* Ajuster le footer pour qu'il ne soit pas caché */
            .pub-footer {
                margin-bottom: 0;
            }
        }

        @media (max-width: 480px) {
            .bottom-nav .nav-item {
                min-width: 44px;
                padding: 2px 8px;
            }
            .bottom-nav .nav-item i {
                font-size: 20px;
            }
            .bottom-nav .nav-item span {
                font-size: 8px;
            }
            .bottom-nav .nav-item.publier {
                padding: 4px 12px;
                min-width: 56px;
                margin-top: -8px;
            }
            .bottom-nav .nav-item.publier i {
                font-size: 16px;
            }
            .bottom-nav .nav-item.publier span {
                font-size: 9px;
            }
            body {
                padding-bottom: 68px;
            }
        }

        @media (max-width: 380px) {
            .bottom-nav .nav-item {
                min-width: 36px;
                padding: 2px 4px;
            }
            .bottom-nav .nav-item i {
                font-size: 17px;
            }
            .bottom-nav .nav-item span {
                font-size: 7px;
            }
            .bottom-nav .nav-item.publier {
                padding: 4px 8px;
                min-width: 44px;
                margin-top: -6px;
            }
            .bottom-nav .nav-item.publier i {
                font-size: 14px;
            }
            .bottom-nav .nav-item.publier span {
                font-size: 8px;
            }
            body {
                padding-bottom: 60px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- ===== CONTENU PRINCIPAL AVEC ANTI-FLASH ===== -->
<div id="app">

<!-- Header -->
<header class="pub-header @if(Route::currentRouteName() == 'recherche') page-recherche @endif">
    <nav class="pub-nav">
        <a href="{{ route('home') }}" class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name">Doya<span>Immo</span></div>
        </a>

        <!-- Barre de recherche - cachée sur mobile -->
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

        <!-- Liens - UNIQUEMENT SUR DESKTOP -->
        <div class="pub-links">
            <a href="{{ route('besoins.index') }}">Besoins</a>
            <a href="{{ route('biens.index') }}">Biens</a>
            <a href="{{ route('home') }}#comment">Comment ça marche</a>
            <a href="{{ route('register.agence') }}">Pour les agences</a>
        </div>

        <div class="pub-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-user"></i> <span class="hide-mobile">{{ Auth::user()->prenom ?? 'Profil' }}</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm hide-mobile">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hide-mobile">Connexion</a>
                <a href="{{ route('register') }}" class="btn btn-rust btn-sm">
                    <i class="fa-solid fa-plus"></i> <span class="hide-mobile">Publier</span>
                </a>
            @endauth
        </div>

        <!-- Icônes recherche + menu regroupées (mobile) -->
        <div class="mobile-icons">
            <button class="search-toggle-btn" id="searchToggleBtn" onclick="openSearch()" aria-label="Rechercher">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <button class="pub-burger" id="pubBurger" onclick="toggleMobileMenu()" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- Menu mobile (hamburger) -->
    <div class="pub-mobile-menu" id="pubMobileMenu" role="navigation">
        <a href="{{ route('besoins.index') }}" class="menu-item">
            <span class="menu-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <span class="menu-text">Parcourir les besoins</span>
            <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('biens.index') }}" class="menu-item">
            <span class="menu-icon"><i class="fa-solid fa-house"></i></span>
            <span class="menu-text">Parcourir les biens</span>
            <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('home') }}#comment" class="menu-item">
            <span class="menu-icon"><i class="fa-solid fa-question-circle"></i></span>
            <span class="menu-text">Comment ça marche</span>
            <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('register.agence') }}" class="menu-item">
            <span class="menu-icon"><i class="fa-solid fa-building"></i></span>
            <span class="menu-text">Pour les agences</span>
            <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </a>

        <div class="menu-divider"></div>

        @auth
            <a href="{{ route('dashboard') }}" class="menu-item">
                <span class="menu-icon"><i class="fa-solid fa-gauge-high"></i></span>
                <span class="menu-text">Tableau de bord</span>
                <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="menu-item">
                    <span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span class="menu-text">Déconnexion</span>
                    <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                </button>
            </form>
        @else
            <div class="menu-auth">
                <a href="{{ route('login') }}" class="menu-item">
                    <span class="menu-icon"><i class="fa-solid fa-arrow-right-to-bracket"></i></span>
                    <span class="menu-text">Se connecter</span>
                    <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
                </a>
            </div>
            <a href="{{ route('register.particulier') }}" class="menu-item">
                <span class="menu-icon"><i class="fa-solid fa-user-plus"></i></span>
                <span class="menu-text">Publier un besoin</span>
                <span class="menu-arrow"><i class="fa-solid fa-chevron-right"></i></span>
            </a>
        @endauth
    </div>

    <!-- Overlay de recherche mobile -->
    <div class="search-overlay" id="searchOverlay" onclick="closeSearchOutside(event)">
        <div class="search-modal">
            <button class="search-close" onclick="closeSearch()">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input 
                    type="text" 
                    id="mobileSearchInput" 
                    placeholder="Rechercher un besoin, un bien, une agence..."
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
                    <li><a href="{{ route('register.agence') }}">Devenir agence partenaire</a></li>
                </ul>
            </div>
            <div>
                <h4>Ressources</h4>
                <ul>
                    <li><a href="#">Conditions d'utilisation</a></li>
                    <li><a href="#">Mentions légales</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:contact@doyaimmo.sn">contact@doyaimmo.sn</a></li>
                    <li><a href="tel:+221774612082">+221 77 461 20 82</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} DoyaImmo. Tous droits réservés.</span>
        </div>
    </div>
</footer>

<!-- ============================================
     BOTTOM NAVIGATION (MENU MOBILE)
     ============================================ -->
<nav class="bottom-nav" role="navigation" aria-label="Navigation principale">
    <div class="nav-inner">
        <!-- Accueil -->
        <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Accueil</span>
        </a>

        <!-- Biens -->
        <a href="{{ route('biens.index') }}" class="nav-item {{ request()->routeIs('biens.*') ? 'active' : '' }}">
            <i class="fa-solid fa-building"></i>
            <span>Bien</span>
            @if(isset($nbBiens) && $nbBiens > 0)
                <span class="badge-count">{{ $nbBiens }}</span>
            @endif
        </a>

        <!-- PUBLIER (au milieu) -->
        @auth
            @if(Auth::user()->isAgence())
                <a href="{{ route('agence.biens.create') }}" class="nav-item publier">
                    <i class="fa-solid fa-plus"></i>
                    <span>Publier</span>
                </a>
            @elseif(Auth::user()->isParticulier())
                <a href="{{ route('particulier.demandes.create') }}" class="nav-item publier">
                    <i class="fa-solid fa-plus"></i>
                    <span>Publier</span>
                </a>
            @else
                <a href="{{ route('register') }}" class="nav-item publier">
                    <i class="fa-solid fa-plus"></i>
                    <span>Publier</span>
                </a>
            @endif
        @else
            <a href="{{ route('register') }}" class="nav-item publier">
                <i class="fa-solid fa-plus"></i>
                <span>Publier</span>
            </a>
        @endauth

        <!-- Besoins -->
        <a href="{{ route('besoins.index') }}" class="nav-item {{ request()->routeIs('besoins.*') ? 'active' : '' }}">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span>Besoins</span>
            @if(isset($nbBesoins) && $nbBesoins > 0)
                <span class="badge-count">{{ $nbBesoins }}</span>
            @endif
        </a>

        <!-- Agences -->
        <a href="{{ route('agences.public.index') }}" class="nav-item {{ request()->routeIs('agences.*') ? 'active' : '' }}">
            <i class="fa-solid fa-building-columns"></i>
            <span>Agence</span>
        </a>
    </div>
</nav>

<!-- Flèche Retour en Haut (Floating) -->
<button class="back-to-top" id="backToTop" onclick="scrollToTop()" aria-label="Retour en haut">
    <i class="fa-solid fa-chevron-up"></i>
</button>

</div><!-- Fin #app -->

<!-- Scripts -->
<script>
    // ============================================
    // ANTI-FLASH : AFFICHER LE CONTENU
    // ============================================
    (function() {
        // Fonction pour afficher le contenu
        function showContent() {
            var app = document.getElementById('app');
            if (app) {
                app.classList.add('ready');
            }
            // Afficher le main
            var main = document.querySelector('main');
            if (main) {
                main.classList.add('visible');
            }
            // Afficher la bottom nav
            var bottomNav = document.querySelector('.bottom-nav');
            if (bottomNav) {
                bottomNav.classList.add('visible');
            }
        }

        // Essayer d'afficher immédiatement si le DOM est déjà chargé
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(showContent, 50);
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(showContent, 50);
            });
        }

        // Fallback: afficher après le chargement complet
        window.addEventListener('load', function() {
            setTimeout(showContent, 100);
        });

        // Fallback: afficher après un délai maximum (sécurité)
        setTimeout(showContent, 500);
    })();

    // ==================== TOGGLE MOBILE MENU ====================
    function toggleMobileMenu() {
        const menu = document.getElementById('pubMobileMenu');
        const burger = document.getElementById('pubBurger');
        const isOpen = menu.classList.toggle('open');
        burger.classList.toggle('active');
        burger.setAttribute('aria-expanded', isOpen);
        
        if (isOpen) {
            menu.style.maxHeight = menu.scrollHeight + 'px';
        } else {
            menu.style.maxHeight = '0';
        }
    }

    document.querySelectorAll('#pubMobileMenu .menu-item').forEach(link => {
        link.addEventListener('click', () => {
            const menu = document.getElementById('pubMobileMenu');
            const burger = document.getElementById('pubBurger');
            menu.classList.remove('open');
            burger.classList.remove('active');
            burger.setAttribute('aria-expanded', 'false');
            menu.style.maxHeight = '0';
        });
    });

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('pubMobileMenu');
        const burger = document.getElementById('pubBurger');
        if (menu.classList.contains('open') && 
            !e.target.closest('.pub-nav') && 
            !e.target.closest('.pub-mobile-menu')) {
            menu.classList.remove('open');
            burger.classList.remove('active');
            burger.setAttribute('aria-expanded', 'false');
            menu.style.maxHeight = '0';
        }
    });

    // ==================== RECHERCHE MOBILE ====================
    function openSearch() {
        const overlay = document.getElementById('searchOverlay');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            document.getElementById('mobileSearchInput').focus();
        }, 300);
    }

    function closeSearch() {
        const overlay = document.getElementById('searchOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function closeSearchOutside(event) {
        if (event.target === event.currentTarget) {
            closeSearch();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const mobileInput = document.getElementById('mobileSearchInput');
        if (mobileInput) {
            mobileInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const query = this.value.trim();
                    if (query.length > 0) {
                        window.location.href = `/recherche?q=${encodeURIComponent(query)}`;
                    }
                }
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSearch();
        }
    });

    // ==================== RECHERCHE HEADER ====================
    function performSearch() {
        const query = document.getElementById('headerSearch').value.trim();
        if (query.length > 0) {
            window.location.href = `/recherche?q=${encodeURIComponent(query)}`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const headerSearch = document.getElementById('headerSearch');
        const headerResults = document.getElementById('headerAutocomplete');
        let debounceTimer;

        if (headerSearch) {
            document.addEventListener('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    headerSearch.focus();
                    headerSearch.select();
                }
                if (e.key === 'Escape') {
                    headerResults.style.display = 'none';
                    headerSearch.blur();
                }
            });

            headerSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
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
                    fetch(`/recherche/autocomplete?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                headerResults.innerHTML = `
                                    <div class="autocomplete-empty">
                                        <i class="fa-regular fa-search"></i>
                                        Aucun résultat trouvé pour "<strong>${query}</strong>"
                                    </div>
                                `;
                                headerResults.style.display = 'block';
                                return;
                            }
                            renderResults(data);
                        })
                        .catch(() => {
                            headerResults.style.display = 'none';
                        });
                }, 250);
            });
        }

        function renderResults(data) {
            const typeIcons = {
                'besoin': 'fa-solid fa-magnifying-glass',
                'bien': 'fa-solid fa-building',
                'agence': 'fa-solid fa-building-columns',
                'quartier': 'fa-solid fa-location-dot'
            };

            const typeClasses = {
                'besoin': 'besoin',
                'bien': 'bien',
                'agence': 'agence',
                'quartier': 'quartier'
            };

            headerResults.innerHTML = data.map(item => {
                const icon = typeIcons[item.type] || 'fa-solid fa-circle';
                const cls = typeClasses[item.type] || 'besoin';
                return `
                    <div class="autocomplete-item" onclick="window.location.href='${item.url || '#'}'">
                        <div class="item-icon ${cls}">
                            <i class="${icon}"></i>
                        </div>
                        <div class="item-content">
                            <div class="item-title">${item.label}</div>
                            <div class="item-desc">${item.description || ''}</div>
                        </div>
                        <span class="item-type">${item.type || 'Autre'}</span>
                    </div>
                `;
            }).join('');

            headerResults.style.display = 'block';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-input-wrapper')) {
                headerResults.style.display = 'none';
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                headerResults.style.display = 'none';
            }
        });
    });

    // ==================== BACK TO TOP ====================
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    document.addEventListener('scroll', function() {
        const btn = document.getElementById('backToTop');
        if (btn) {
            if (window.scrollY > 300) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        }
    });
</script>

@stack('scripts')

<!-- ============================================
     ✅ AJOUT SEO : STACK JSON-LD
     ============================================ -->
@stack('jsonld')
<!-- ========================================== -->

</body>
</html>