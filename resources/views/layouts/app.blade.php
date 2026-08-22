<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* ============================================
           STYLES CRITIQUES POUR ÉVITER LE FOUC
           ============================================ */
        body {
            background: #F7F9FC;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            opacity: 0;
            transition: opacity 0.15s ease;
        }
        body.loaded {
            opacity: 1;
        }
        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
            width: 100%;
        }

        /* ============================================
           RESET & BASE
           ============================================ */
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
            --gold-soft: rgba(184, 150, 40, 0.12);
            --green-soft: rgba(30, 122, 71, 0.12);
            --radius: 16px;
            --display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --header-height: 70px;
            --container-padding: clamp(16px, 4vw, 24px);
        }

        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #F7F9FC;
            color: var(--ink);
            line-height: 1.6;
            overflow-x: hidden;
        }

        img, svg, video {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* ============================================
           CONTAINER
           ============================================ */
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
            font-family: inherit;
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
           NAV LINKS
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
           MOBILE MENU
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
            font-family: inherit;
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
            font-family: inherit;
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

            .field input,
            .field select,
            .field textarea,
            .form-control,
            .auth-box input,
            .auth-box select,
            .auth-box textarea,
            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="password"],
            input[type="number"],
            textarea,
            select {
                font-size: 16px !important;
            }

            .password-wrapper input {
                font-size: 16px !important;
            }

            input::placeholder,
            textarea::placeholder {
                font-size: 14px !important;
            }
        }

        /* ============================================
           HERO
           ============================================ */
        .hero {
            padding: clamp(40px, 8vw, 64px) 0 clamp(50px, 10vw, 90px);
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
            pointer-events: none;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: clamp(30px, 5vw, 50px);
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-illustration {
            background: var(--ink);
            border-radius: 20px;
            padding: clamp(20px, 3vw, 26px);
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
            pointer-events: none;
        }

        .mini-card {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
            word-break: break-word;
        }

        .mini-card:last-child {
            margin-bottom: 0;
        }

        .mini-card .t {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(13px, 1.1vw, 14px);
            margin-bottom: 4px;
        }

        .mini-card .s {
            font-size: clamp(11px, 0.9vw, 12px);
            color: #9AA1AB;
        }

        .hero-stats {
            display: flex;
            gap: clamp(20px, 4vw, 40px);
            margin-top: clamp(24px, 3vw, 32px);
            flex-wrap: wrap;
        }

        .hero-stats .stat-item {
            display: flex;
            flex-direction: column;
        }

        .hero-stats .stat-number {
            font-family: var(--display);
            font-size: clamp(18px, 2vw, 22px);
            font-weight: 700;
            color: var(--ink);
        }

        .hero-stats .stat-label {
            font-size: clamp(11px, 0.9vw, 13px);
            color: var(--muted);
        }

        /* ============================================
           TYPOGRAPHY
           ============================================ */
        .eyebrow {
            display: inline-block;
            font-size: clamp(11px, 0.9vw, 12px);
            font-weight: 600;
            color: var(--rust);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .h-hero {
            font-family: var(--display);
            font-weight: 800;
            font-size: clamp(28px, 5vw, 52px);
            line-height: 1.15;
        }

        .h-section {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(22px, 3.5vw, 34px);
            line-height: 1.2;
        }

        .lead {
            font-size: clamp(15px, 1.2vw, 17px);
            color: var(--text-soft);
            line-height: 1.7;
        }

        .text-center {
            text-align: center;
        }

        /* ============================================
           SECTION HEADER
           ============================================ */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        /* ============================================
           ZONE CHIPS
           ============================================ */
        .zone-row {
            display: flex;
            gap: clamp(10px, 1.5vw, 14px);
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x proximity;
        }

        .zone-row::-webkit-scrollbar {
            height: 4px;
        }

        .zone-row::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }

        .zone-chip {
            flex-shrink: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: clamp(12px, 1.5vw, 16px) clamp(16px, 2vw, 22px);
            text-align: center;
            min-width: clamp(100px, 15vw, 130px);
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            scroll-snap-align: start;
        }

        .zone-chip:hover {
            border-color: var(--rust);
            background: var(--rust-soft);
            transform: translateY(-2px);
        }

        .zone-chip b {
            display: block;
            font-family: var(--display);
            font-size: clamp(13px, 1.1vw, 14px);
            margin-bottom: 3px;
        }

        .zone-chip span {
            font-size: clamp(10px, 0.8vw, 11.5px);
            color: var(--muted);
        }

        /* ============================================
           BIENS GRID
           ============================================ */
        .biens-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(clamp(240px, 25vw, 280px), 1fr));
            gap: clamp(16px, 2vw, 24px);
        }

        .bien-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .bien-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        }

        .bien-image {
            position: relative;
            height: clamp(160px, 25vw, 200px);
            background: #F0F2F5;
            overflow: hidden;
            flex-shrink: 0;
        }

        .bien-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 40px;
            opacity: 0.3;
        }

        .badge-status {
            position: absolute;
            top: clamp(8px, 1vw, 12px);
            right: clamp(8px, 1vw, 12px);
            padding: clamp(3px, 0.4vw, 4px) clamp(10px, 1.2vw, 14px);
            border-radius: 999px;
            font-size: clamp(10px, 0.8vw, 11px);
            font-weight: 600;
            color: #fff;
        }

        .badge-status.disponible {
            background: var(--teal);
        }

        .badge-status.indisponible {
            background: var(--muted);
        }

        .badge-type {
            position: absolute;
            bottom: clamp(8px, 1vw, 12px);
            left: clamp(8px, 1vw, 12px);
            padding: clamp(3px, 0.4vw, 4px) clamp(10px, 1.2vw, 14px);
            border-radius: 999px;
            font-size: clamp(10px, 0.8vw, 11px);
            font-weight: 600;
            color: #fff;
            background: rgba(0,0,0,0.6);
        }

        .bien-body {
            padding: clamp(14px, 1.5vw, 18px);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .bien-title {
            font-weight: 600;
            font-size: clamp(14px, 1.2vw, 16px);
            margin-bottom: 2px;
            color: var(--ink);
            word-break: break-word;
        }

        .bien-price {
            font-weight: 700;
            color: var(--rust);
            font-size: clamp(15px, 1.2vw, 17px);
            margin-bottom: 4px;
        }

        .bien-location {
            font-size: clamp(12px, 0.9vw, 13px);
            color: var(--muted);
            margin-bottom: 8px;
            word-break: break-word;
        }

        .bien-location i {
            margin-right: 4px;
        }

        .bien-features {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .feature-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--border);
            padding: 2px 12px;
            border-radius: 999px;
            font-size: clamp(10px, 0.8vw, 11px);
            color: var(--text-soft);
            white-space: nowrap;
        }

        .feature-pill i {
            font-size: 10px;
        }

        .bien-agency {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: clamp(11px, 0.8vw, 12px);
            color: var(--muted);
            margin-bottom: 12px;
            word-break: break-word;
        }

        .bien-body .btn {
            margin-top: auto;
        }

        /* ============================================
           HOW GRID
           ============================================ */
        .how-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(240px, 30vw, 350px), 1fr));
            gap: clamp(16px, 2vw, 24px);
            margin-top: 36px;
        }

        .how-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: clamp(20px, 2.5vw, 26px);
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
            flex-shrink: 0;
        }

        .how-card h3 {
            font-family: var(--display);
            font-size: clamp(15px, 1.2vw, 16px);
            margin-bottom: 8px;
        }

        .how-card p {
            font-size: clamp(13px, 1vw, 13.5px);
            color: var(--text-soft);
            line-height: 1.65;
        }

        /* ============================================
           VALUE SECTION - POURQUOI DOYAIMMO
           ============================================ */
        .value-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }

        @media (min-width: 768px) {
            .value-container {
                padding: 0 24px;
            }
        }

        @media (min-width: 1200px) {
            .value-container {
                padding: 0 40px;
            }
        }

        .value-subtitle {
            font-size: clamp(14px, 1vw, 16px);
            color: var(--muted);
            margin-top: 6px;
            max-width: 550px;
            margin-left: auto;
            margin-right: auto;
        }

        .value-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(200px, 22vw, 250px), 1fr));
            gap: clamp(16px, 2vw, 20px);
            margin-top: 30px;
        }

        .value-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 24px 20px;
            text-align: center;
        }

        .value-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.06);
        }

        .value-ic {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 20px;
            flex-shrink: 0;
        }

        .value-card h3 {
            font-family: var(--display);
            font-size: clamp(14px, 1.1vw, 15px);
            margin-bottom: 6px;
            color: var(--ink);
        }

        .value-card p {
            font-size: clamp(12px, 0.9vw, 13px);
            color: var(--text-soft);
            line-height: 1.6;
            max-width: 300px;
            margin: 0 auto;
        }

        /* ============================================
           CTA BAND
           ============================================ */
        .cta-band {
            background: var(--ink);
            border-radius: clamp(16px, 2vw, 24px);
            padding: clamp(30px, 5vw, 56px) clamp(20px, 4vw, 56px);
            text-align: center;
            color: #fff;
        }

        .cta-band h2 {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(22px, 3vw, 28px);
            margin-bottom: 14px;
        }

        .cta-band p {
            font-size: clamp(13px, 1vw, 14.5px);
            color: #B9BFCC;
            max-width: 480px;
            margin: 0 auto 26px;
        }

        .cta-actions {
            display: flex;
            gap: clamp(12px, 1.5vw, 16px);
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-actions .btn {
            flex: 1 1 auto;
            min-width: 180px;
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
           PAGINATION
           ============================================ */
        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .pagination li {
            display: inline;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: clamp(6px, 0.6vw, 8px) clamp(10px, 1vw, 14px);
            border-radius: 8px;
            border: 1px solid var(--border);
            color: var(--text-soft);
            text-decoration: none;
            font-size: clamp(12px, 0.9vw, 13px);
            transition: all 0.2s;
            min-width: clamp(32px, 3.5vw, 40px);
            text-align: center;
        }

        .pagination a:hover {
            background: var(--border);
        }

        .pagination .active span {
            background: var(--rust);
            color: #fff;
            border-color: var(--rust);
        }

        .pagination .disabled span {
            opacity: 0.5;
            cursor: not-allowed;
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
           EMPTY STATE
           ============================================ */
        .empty-state {
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border);
            color: var(--muted);
        }

        .empty-state i {
            font-size: 40px;
            display: block;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        .empty-state p {
            font-size: clamp(13px, 1vw, 14px);
        }

        /* ============================================
           DETAIL
           ============================================ */
        .detail-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: clamp(20px, 3vw, 30px);
            align-items: start;
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
            font-size: clamp(12px, 0.9vw, 13.5px);
            flex-wrap: wrap;
            gap: 8px;
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
            font-size: clamp(11px, 0.8vw, 12.5px);
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 999px;
        }

        .sticky-box {
            position: sticky;
            top: 100px;
        }

        /* ============================================
           FILTER BAR
           ============================================ */
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
            font-size: clamp(13px, 1vw, 14px);
            font-family: inherit;
            color: var(--ink);
            min-width: 140px;
            flex: 1 1 auto;
            max-width: 100%;
        }

        .filter-bar .grow {
            flex: 1;
            min-width: 160px;
        }

        /* ============================================
           RESPONSIVE GLOBAL
           ============================================ */
        @media (max-width: 900px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .hero-illustration {
                order: 2;
            }

            .hero-grid > div:first-child {
                order: 1;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .sticky-box {
                position: static;
            }
        }

        @media (max-width: 820px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
            .section {
                padding: 30px 0;
            }

            .hero {
                padding: 30px 0 40px;
            }

            .value-grid {
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }

            .value-card {
                padding: 18px 14px;
            }

            .value-ic {
                width: 40px;
                height: 40px;
                font-size: 17px;
            }

            .value-card p {
                max-width: 100%;
                font-size: 12px;
            }
        }

        @media (max-width: 600px) {
            .hero-grid {
                gap: 24px;
            }

            .hero-stats {
                gap: 16px;
            }

            .cta-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .cta-actions .btn {
                min-width: unset;
                justify-content: center;
            }

            .filter-bar {
                flex-direction: column;
            }

            .filter-bar select,
            .filter-bar input {
                width: 100%;
                min-width: unset;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .biens-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .value-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .value-card {
                padding: 16px 12px;
            }

            .value-ic {
                width: 36px;
                height: 36px;
                font-size: 15px;
            }

            .cta-band {
                padding: 24px 16px;
            }

            .cta-band h2 {
                font-size: 22px;
            }

            .pagination a,
            .pagination span {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 32px;
            }

            .hero-illustration {
                padding: 16px;
                border-radius: 16px;
            }
        }

        @media (max-width: 360px) {
            .h-hero {
                font-size: 24px;
            }

            .zone-chip {
                min-width: 88px;
                padding: 10px 12px;
            }
        }

        /* ============================================
           ACCESSIBILITÉ
           ============================================ */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ============================================
           UTILITAIRES
           ============================================ */
        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: clamp(16px, 2vw, 24px);
        }

        .results-count {
            font-size: clamp(12px, 0.9vw, 13px);
            color: var(--muted);
            margin-bottom: 18px;
        }

        .page-head {
            padding: 44px 0 10px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--border);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: clamp(11px, 0.8vw, 12px);
            color: var(--text-soft);
        }

        .tier-bar {
            height: 4px;
        }
        .tier-prem { background: #F5A623; }
        .tier-std { background: #4A90D9; }
        .tier-eco { background: #7ED321; }

        .besoin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(clamp(260px, 28vw, 300px), 1fr));
            gap: clamp(16px, 2vw, 24px);
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

        .besoin-body {
            padding: clamp(16px, 1.5vw, 20px);
        }

        .besoin-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .besoin-type {
            font-family: var(--display);
            font-weight: 700;
            font-size: clamp(14px, 1.1vw, 16px);
        }

        .besoin-budget {
            font-weight: 700;
            color: var(--rust);
            font-size: clamp(13px, 1vw, 14px);
        }

        .besoin-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .besoin-desc {
            font-size: clamp(12px, 0.9vw, 13.5px);
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
            flex-wrap: wrap;
            gap: 8px;
        }

        .posted {
            font-size: clamp(11px, 0.8vw, 12px);
            color: var(--muted);
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
           FLÈCHE RETOUR EN HAUT (FLOATING)
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

        /* ===== RESPONSIVE FOOTER ===== */
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
    </style>

    @stack('styles')
</head>
<body>

<!-- ============================================
     SCRIPT POUR LE CHARGEMENT
     ============================================ -->
<script>
    // Ajouter la classe 'loaded' au body après le chargement
    document.addEventListener('DOMContentLoaded', function() {
        document.body.classList.add('loaded');
    });
    // Fallback si DOMContentLoaded est déjà passé
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        document.body.classList.add('loaded');
    }
</script>

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

    <!-- Menu mobile -->
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
                    <li><a href="#">Politique de confidentialité</a></li>
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
    </div>
</footer>

<!-- Flèche Retour en Haut (Floating) -->
<button class="back-to-top" id="backToTop" onclick="scrollToTop()" aria-label="Retour en haut">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<!-- Scripts -->
<script>
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

    // Recherche avec la touche Entrée
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

    // Fermer la recherche avec Echap
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
                    fetch(`/api/search/autocomplete?q=${encodeURIComponent(query)}`)
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

    // Afficher/masquer la flèche selon le défilement
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

</body>
</html>