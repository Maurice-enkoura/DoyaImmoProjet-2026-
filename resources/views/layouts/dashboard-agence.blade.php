<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Agence — DoyaImmo')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ═══════════════════════════════════════════════════════
           VARIABLES GLOBALES
        ═══════════════════════════════════════════════════════ */
        :root {
            --rust:      #B5502A;
            --rust-soft: rgba(181, 80, 42, 0.12);
            --ink:       #1A1D26;
            --ink-soft:  #20262F;
            --surface:   #FFFFFF;
            --bg:        #F7F9FC;
            --border:    #E8ECF0;
            --text-soft: #4A5260;
            --muted:     #8A91A0;

            --c-success:    #1E7A47;
            --c-success-bg: #E8F5E9;
            --c-warning:    #E65100;
            --c-warning-bg: #FFF8E1;
            --c-danger:     #C62828;
            --c-danger-bg:  #FFEBEE;
            --c-info:       #0D47A1;
            --c-info-bg:    #E3F2FD;
            --c-gold:       #F5A623;
            --c-gold-bg:    #FFF8E1;

            --radius: 16px;
            --radius-sm: 10px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, .04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, .08);
            --shadow-lg: 0 16px 48px rgba(0, 0, 0, .12);

            --display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
        }

        .app { display: flex; min-height: 100vh; }

        /* ═══════════════════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════════════════ */
        .sidebar {
            width: 260px;
            background: var(--ink);
            color: #fff;
            padding: 24px 18px;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s ease;
            overflow-y: auto;
            height: 100vh;
            padding-bottom: 40px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.15) transparent;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .15);
            border-radius: 999px;
        }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            margin-bottom: 24px;
            padding: 6px;
            border-radius: 10px;
            transition: background .2s;
        }
        .brand:hover { background: rgba(255, 255, 255, .05); }

        .brand-mark {
            width: 38px;
            height: 38px;
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
            box-shadow: 0 6px 14px rgba(181, 80, 42, .3);
        }
        .brand-name {
            font-family: var(--display);
            font-weight: 700;
            font-size: 17px;
            color: #fff;
            letter-spacing: -.3px;
        }
        .brand-name span { color: #d4754a; }

        /* Navigation */
        .nav-group-label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #6A7280;
            margin: 18px 8px 8px;
            font-weight: 700;
        }

        .navlist {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .navlink {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #B9BFCC;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .2s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }
        .navlink:hover {
            background: rgba(255, 255, 255, .06);
            color: #fff;
        }

        .navlink.active {
            background: rgba(255, 255, 255, .1);
            color: #fff;
            font-weight: 600;
        }
        .navlink.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            border-radius: 0 3px 3px 0;
            background: var(--rust);
        }
        .navlink.active .ic { color: var(--rust); }

        .navlink .ic {
            width: 20px;
            text-align: center;
            font-size: 15px;
            flex-shrink: 0;
            transition: color .2s;
        }

        .navlink .badge {
            margin-left: auto;
            background: var(--rust);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 1px 9px;
            border-radius: 999px;
            flex-shrink: 0;
            font-family: var(--display);
            min-width: 22px;
            text-align: center;
        }

        /* Plan card */
        .plan-card {
            background: rgba(255, 255, 255, .06);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, .08);
        }
        .plan-card .tier {
            font-family: var(--display);
            font-weight: 700;
            font-size: 13.5px;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .plan-card .tier i { color: var(--c-gold); font-size: 12px; }
        .plan-card .desc {
            font-size: 11.5px;
            color: #8A91A0;
            margin: 6px 0 8px;
            line-height: 1.5;
        }
        .plan-card .desc strong {
            color: #fff;
            font-weight: 700;
            font-family: var(--display);
        }
        .plan-card a {
            color: var(--rust);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s;
        }
        .plan-card a:hover { color: #d4754a; }

        /* Footer sidebar */
        .sidebar-foot {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, .06);
        }

        .agency-mini {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px;
            border-radius: 11px;
            background: rgba(255, 255, 255, .04);
            transition: background .2s;
        }
        .agency-mini:hover { background: rgba(255, 255, 255, .08); }

        .agency-mini .av {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--display);
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
            letter-spacing: .5px;
        }
        .agency-mini .name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }
        .agency-mini .role {
            font-size: 11px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .agency-mini .role i { font-size: 9px; }

        /* ═══════════════════════════════════════════════════════
           MAIN
        ═══════════════════════════════════════════════════════ */
        .main {
            margin-left: 260px;
            flex: 1;
            padding: 22px 30px 40px;
            min-height: 100vh;
            width: calc(100% - 260px);
        }

        /* Topbar */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .topbar__left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }

        .burger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: background .2s;
        }
        .burger:hover { background: var(--border); }
        .burger span {
            display: block;
            width: 22px;
            height: 2px;
            background: var(--ink);
            margin: 4px 0;
            transition: .2s;
            border-radius: 2px;
        }

        .topbar__titles { min-width: 0; }
        .topbar__title {
            font-family: var(--display);
            font-weight: 700;
            font-size: 19px;
            color: var(--ink);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .topbar__sub {
            font-size: 12.5px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }

        .topbar__actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Icon buttons */
        .icon-btn {
            position: relative;
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
            transition: all .2s;
            font-family: inherit;
        }
        .icon-btn:hover {
            border-color: var(--rust);
            color: var(--rust);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .icon-btn__badge {
            position: absolute;
            top: -3px;
            right: -3px;
            background: var(--c-danger);
            color: #fff;
            font-size: 9.5px;
            font-weight: 800;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            font-family: var(--display);
        }

        /* Avatar topbar */
        .topbar-avatar-wrapper { position: relative; }
        .topbar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-family: var(--display);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            box-shadow: 0 4px 10px rgba(181, 80, 42, .2);
            letter-spacing: .3px;
        }
        .topbar-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 14px rgba(181, 80, 42, .3);
        }

        /* ═══════════════════════════════════════════════════════
           DROPDOWNS
        ═══════════════════════════════════════════════════════ */
        .dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 380px;
            max-width: calc(100vw - 32px);
            max-height: 460px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            animation: dropdownIn .2s ease;
        }
        .dropdown.open { display: block; }

        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .dropdown__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            font-size: 14px;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
            border-radius: var(--radius) var(--radius) 0 0;
            font-family: var(--display);
        }
        .dropdown__head button {
            background: none;
            border: none;
            color: var(--rust);
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: inherit;
        }
        .dropdown__head button:hover { background: var(--rust-soft); }

        .dropdown__item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 12px 18px;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            color: var(--ink);
            font-size: 13px;
            transition: background .15s;
            cursor: pointer;
        }
        .dropdown__item:last-child { border-bottom: none; }
        .dropdown__item:hover { background: var(--bg); }
        .dropdown__item.is-unread {
            background: rgba(181, 80, 42, .04);
            border-left: 3px solid var(--rust);
        }
        .dropdown__item.is-unread:hover { background: rgba(181, 80, 42, .08); }

        .dropdown__item-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
        }
        .dropdown__item-icon.info    { background: var(--c-info-bg);    color: var(--c-info); }
        .dropdown__item-icon.success { background: var(--c-success-bg); color: var(--c-success); }
        .dropdown__item-icon.warning { background: var(--c-warning-bg); color: var(--c-warning); }
        .dropdown__item-icon.danger  { background: var(--c-danger-bg);  color: var(--c-danger); }

        .dropdown__item-content { flex: 1; min-width: 0; }
        .dropdown__item-title {
            font-weight: 600;
            font-size: 13px;
            color: var(--ink);
        }
        .dropdown__item-desc {
            font-size: 12.5px;
            color: var(--text-soft);
            margin-top: 2px;
            line-height: 1.5;
        }
        .dropdown__item-time {
            font-size: 11px;
            color: var(--muted);
            margin-top: 4px;
        }

        .dropdown__empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--muted);
        }
        .dropdown__empty i {
            font-size: 32px;
            display: block;
            margin-bottom: 12px;
            opacity: .3;
        }
        .dropdown__empty span { font-size: 13px; }

        .dropdown__footer {
            padding: 10px 18px;
            border-top: 1px solid var(--border);
            text-align: center;
            position: sticky;
            bottom: 0;
            background: #fff;
            border-radius: 0 0 var(--radius) var(--radius);
        }
        .dropdown__footer a {
            color: var(--rust);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }
        .dropdown__footer a:hover { text-decoration: underline; }

        /* User dropdown */
        .user-dropdown { width: 260px; padding: 0; }
        .user-dropdown__head {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
        }
        .user-dropdown__avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--display);
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 10px;
            letter-spacing: .3px;
        }
        .user-dropdown__name {
            font-family: var(--display);
            font-weight: 700;
            font-size: 14px;
            color: var(--ink);
        }
        .user-dropdown__email {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }
        .user-dropdown__badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 700;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .user-dropdown__badge.is-verified {
            background: var(--c-success-bg);
            color: var(--c-success);
        }
        .user-dropdown__badge.is-pending {
            background: var(--c-warning-bg);
            color: var(--c-warning);
        }

        .user-dropdown__item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 18px;
            color: var(--text-soft);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background .15s, color .15s;
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            width: 100%;
            text-align: left;
        }
        .user-dropdown__item:hover {
            background: var(--bg);
            color: var(--ink);
        }
        .user-dropdown__item i {
            width: 16px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
            transition: color .15s;
        }
        .user-dropdown__item:hover i { color: var(--rust); }
        .user-dropdown__item.is-danger { color: var(--c-danger); }
        .user-dropdown__item.is-danger i { color: var(--c-danger); }
        .user-dropdown__item.is-danger:hover { background: var(--c-danger-bg); }

        /* ═══════════════════════════════════════════════════════
           FLASH / TOAST
        ═══════════════════════════════════════════════════════ */
        .flash-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }
        .flash {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid;
            animation: flashIn .3s ease;
            position: relative;
        }
        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .flash.is-out { animation: flashOut .3s ease forwards; }
        @keyframes flashOut {
            to { opacity: 0; transform: translateX(100%); height: 0; padding: 0; margin: 0; }
        }
        .flash--success { background: var(--c-success-bg); color: var(--c-success); border-color: #C8E6C9; }
        .flash--error   { background: var(--c-danger-bg);  color: var(--c-danger);  border-color: #FFCDD2; }
        .flash--info    { background: var(--c-info-bg);    color: var(--c-info);    border-color: #BBDEFB; }
        .flash--warning { background: var(--c-warning-bg); color: var(--c-warning); border-color: #FFE0B2; }

        .flash i:first-child { font-size: 15px; flex-shrink: 0; }
        .flash__close {
            margin-left: auto;
            background: none;
            border: none;
            color: currentColor;
            opacity: .6;
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
            border-radius: 6px;
            transition: opacity .15s;
        }
        .flash__close:hover { opacity: 1; }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 380px;
            width: calc(100% - 40px);
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            background: #fff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--rust);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideInRight .35s ease;
        }
        @keyframes slideInRight {
            from { transform: translateX(110%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        .toast.is-out { animation: slideOutRight .3s ease forwards; }
        @keyframes slideOutRight {
            to { transform: translateX(110%); opacity: 0; }
        }
        .toast--success { border-left-color: var(--c-success); }
        .toast--warning { border-left-color: var(--c-warning); }
        .toast--info    { border-left-color: var(--c-info); }
        .toast--danger  { border-left-color: var(--c-danger); }

        .toast__icon { font-size: 19px; flex-shrink: 0; margin-top: 1px; }
        .toast__content { flex: 1; min-width: 0; }
        .toast__title { font-weight: 700; font-size: 13.5px; color: var(--ink); }
        .toast__message { font-size: 12.5px; color: var(--text-soft); margin-top: 2px; line-height: 1.5; }
        .toast__close {
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 14px;
            padding: 2px;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .toast__close:hover { color: var(--ink); background: var(--bg); }

        /* ═══════════════════════════════════════════════════════
           CONTENT
        ═══════════════════════════════════════════════════════ */
        .content { max-width: 1200px; width: 100%; }
        .view { display: none; }
        .view.active { display: block; }

        /* ═══════════════════════════════════════════════════════
           OVERLAY
        ═══════════════════════════════════════════════════════ */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(2px);
            z-index: 99;
            animation: overlayIn .2s ease;
        }
        .overlay.open { display: block; }
        @keyframes overlayIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* ═══════════════════════════════════════════════════════
           BOTTOM NAV (MOBILE)
        ═══════════════════════════════════════════════════════ */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid var(--border);
            z-index: 1001;
            padding: 6px 0 env(safe-area-inset-bottom, 8px) 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, .06);
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
            gap: 3px;
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
            font-size: 21px;
            transition: transform .2s;
        }
        .bottom-nav__item.active { color: var(--rust); }
        .bottom-nav__item.active i { transform: scale(1.1); }

        .bottom-nav__badge {
            position: absolute;
            top: 0;
            right: 2px;
            background: var(--c-danger);
            color: #fff;
            font-size: 9px;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 999px;
            min-width: 17px;
            text-align: center;
            border: 2px solid #fff;
            line-height: 1.3;
            font-family: var(--display);
        }

        /* Bouton Publier central */
        .bottom-nav__item.publier {
            background: linear-gradient(135deg, var(--rust), #d4754a);
            color: #fff;
            border-radius: 999px;
            padding: 8px 18px;
            min-width: 74px;
            box-shadow: 0 6px 16px rgba(181, 80, 42, .35);
            margin-top: -12px;
            transition: transform .2s, box-shadow .2s;
        }
        .bottom-nav__item.publier i { font-size: 17px; }
        .bottom-nav__item.publier span { font-size: 10px; }
        .bottom-nav__item.publier:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(181, 80, 42, .45);
        }
        .bottom-nav__item.publier:active { transform: scale(.95); }

        /* ═══════════════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════════════ */

        @media (max-width: 1024px) {
            .main { padding: 20px 22px 32px; }
        }

        @media (max-width: 820px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.open { transform: translateX(0); }

            .main {
                margin-left: 0;
                padding: 16px 18px 90px;
                width: 100%;
            }

            .burger { display: block; }
            .bottom-nav { display: block; }

            .topbar__title { font-size: 17px; }
            .topbar__sub   { font-size: 12px; }
        }

        @media (max-width: 600px) {
            .main { padding: 14px 14px 90px; }

            .topbar { gap: 10px; margin-bottom: 18px; padding-bottom: 14px; }
            .topbar__title { font-size: 16px; }
            .topbar__sub   { font-size: 11.5px; }

            .icon-btn { width: 36px; height: 36px; }
            .icon-btn__badge { font-size: 9px; min-width: 16px; height: 16px; }

            .topbar-avatar { width: 36px; height: 36px; font-size: 13px; }

            .dropdown { width: calc(100vw - 28px); right: -6px; }

            .user-dropdown { width: 240px; right: 0; }

            .flash { padding: 11px 14px; font-size: 12.5px; }
        }

        @media (max-width: 480px) {
            .bottom-nav__item { min-width: 46px; padding: 3px 6px; }
            .bottom-nav__item i { font-size: 19px; }
            .bottom-nav__item span { font-size: 9px; }
            .bottom-nav__item.publier { padding: 6px 14px; min-width: 62px; margin-top: -10px; }
            .bottom-nav__item.publier i { font-size: 15px; }
        }

        @media (max-width: 380px) {
            .bottom-nav__item { min-width: 40px; padding: 2px 4px; }
            .bottom-nav__item i { font-size: 17px; }
            .bottom-nav__item span { font-size: 8px; }
            .bottom-nav__item.publier { padding: 5px 10px; min-width: 52px; }
            .bottom-nav__item.publier i { font-size: 14px; }
            .bottom-nav__item.publier span { display: none; }
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

    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <div class="app">

        {{-- ═══════════════════════════════════════════
             SIDEBAR
        ═══════════════════════════════════════════ --}}
        <aside class="sidebar" id="sidebar">

            <a href="{{ route('home') }}" class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span>Immo</span></div>
            </a>

            @php
                $agence = Auth::user()->agence ?? null;
                $estValidee = $agence && $agence->statut_validation;
                $agenceNom = $agence ? $agence->nom_agence : 'Agence';
                $agenceInitiales = $agence ? strtoupper(substr($agence->nom_agence, 0, 2)) : 'AG';
                $biensCount = $agence ? $agence->biens()->where('statut', true)->count() : 0;
            @endphp

            @if($estValidee)
                <div class="nav-group-label">Navigation</div>
                <div class="navlist">
                    <a class="navlink" href="{{ route('home') }}" target="_blank">
                        <i class="ic fa-solid fa-house"></i> Accueil
                        <span style="margin-left:auto;font-size:10px;color:#6A7280;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </span>
                    </a>
                </div>

                <div class="nav-group-label">Activité</div>
                <nav class="navlist">
                    <a class="navlink {{ request()->routeIs('agence.dashboard') ? 'active' : '' }}"
                       href="{{ route('agence.dashboard') }}">
                        <i class="ic fa-solid fa-gauge"></i> Tableau de bord
                    </a>

                    <a class="navlink {{ request()->routeIs('agence.demandes.*') ? 'active' : '' }}"
                       href="{{ route('agence.demandes.index') }}">
                        <i class="ic fa-solid fa-house-circle-check"></i> Besoins disponibles
                        @if(($besoinsDisponibles ?? 0) > 0)
                            <span class="badge">{{ $besoinsDisponibles }}</span>
                        @endif
                    </a>

                    <a class="navlink {{ request()->routeIs('agence.propositions.*') ? 'active' : '' }}"
                       href="{{ route('agence.propositions.index') }}">
                        <i class="ic fa-solid fa-file-invoice"></i> Mes offres
                    </a>

                    <a class="navlink {{ request()->routeIs('agence.rendezvous.*') ? 'active' : '' }}"
                       href="{{ route('agence.rendezvous.index') }}">
                        <i class="ic fa-solid fa-calendar-days"></i> Rendez-vous
                        @if(($rendezvousAVenir ?? 0) > 0)
                            <span class="badge">{{ $rendezvousAVenir }}</span>
                        @endif
                    </a>

                    <a class="navlink {{ request()->routeIs('agence.evaluations.*') ? 'active' : '' }}"
                       href="{{ route('agence.evaluations.index') }}">
                        <i class="ic fa-solid fa-star"></i> Avis reçus
                    </a>

                    <a class="navlink {{ request()->routeIs('agence.historique') ? 'active' : '' }}"
                       href="{{ route('agence.historique') }}">
                        <i class="ic fa-solid fa-clock-rotate-left"></i> Historique
                    </a>

                    <a class="navlink {{ request()->routeIs('agence.biens.*') ? 'active' : '' }}"
                       href="{{ route('agence.biens.index') }}">
                        <i class="ic fa-solid fa-building"></i> Publier vos biens
                        @if($biensCount > 0)
                            <span class="badge">{{ $biensCount }}</span>
                        @endif
                    </a>
                </nav>

                <div class="nav-group-label">Compte</div>
                <nav class="navlist">
                    @if(config('abonnement.actif', false))
                        <a class="navlink {{ request()->routeIs('agence.abonnement') ? 'active' : '' }}"
                           href="{{ route('agence.abonnement') }}">
                            <i class="ic fa-solid fa-award"></i> Abonnement
                        </a>
                    @endif

                    <a class="navlink {{ request()->routeIs('agence.profil') ? 'active' : '' }}"
                       href="{{ route('agence.profil') }}">
                        <i class="ic fa-solid fa-user"></i> Profil agence
                    </a>

                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="navlink">
                            <i class="ic fa-solid fa-right-from-bracket"></i> Déconnexion
                        </button>
                    </form>
                </nav>
            @else
                <div class="nav-group-label">Compte</div>
                <nav class="navlist">
                    <a class="navlink {{ request()->routeIs('agence.attente-validation') ? 'active' : '' }}"
                       href="{{ route('agence.attente-validation') }}">
                        <i class="ic fa-solid fa-clock"></i> Validation en cours
                    </a>
                    <a class="navlink" href="{{ route('home') }}" target="_blank">
                        <i class="ic fa-solid fa-house"></i> Accueil
                        <span style="margin-left:auto;font-size:10px;color:#6A7280;">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="navlink">
                            <i class="ic fa-solid fa-right-from-bracket"></i> Déconnexion
                        </button>
                    </form>
                </nav>
            @endif

            <div class="sidebar-foot">
                {{-- Plan card --}}
                @if(config('abonnement.actif', false))
                    <div class="plan-card">
                        <div class="tier">
                            <i class="fa-solid fa-award"></i>
                            {{ $abonnementActuel->formule->label() ?? 'Standard' }}
                        </div>
                        <div class="desc">
                            @if($abonnementActuel)
                                @php
                                    $offresUtilisees = $agence->propositions()
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count();
                                    $limite = $abonnementActuel->formule->limiteOffres();
                                    $limiteAff = $limite === PHP_INT_MAX ? 'Illimité' : $limite;
                                @endphp
                                Jusqu'à <strong>{{ $limiteAff }}</strong> offres / mois
                                <div style="font-size:10px;color:#8A91A0;margin-top:2px;">
                                    {{ $offresUtilisees }} utilisée(s) ce mois
                                </div>
                            @else
                                Aucun abonnement actif
                            @endif
                        </div>
                        <a href="{{ route('agence.abonnement') }}">Voir les plans →</a>
                    </div>
                @endif

                {{-- Agency mini --}}
                <div class="agency-mini">
                    <div class="av">{{ $agenceInitiales }}</div>
                    <div>
                        <div class="name">{{ $agenceNom }}</div>
                        <div class="role" style="color: {{ $estValidee ? '#8A91A0' : '#F5A623' }};">
                            @if($estValidee)
                                <i class="fa-solid fa-circle-check"></i> Agence vérifiée
                            @else
                                <i class="fa-solid fa-clock"></i> En attente
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ═══════════════════════════════════════════
             MAIN
        ═══════════════════════════════════════════ --}}
        <div class="main">

            {{-- Topbar --}}
            <div class="topbar">
                <div class="topbar__left">
                    <button class="burger" onclick="toggleSidebar()" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                    <div class="topbar__titles">
                        <div class="topbar__title">@yield('page_title', 'Tableau de bord')</div>
                        <div class="topbar__sub">@yield('page_sub', 'Vue d\'ensemble de votre activité')</div>
                    </div>
                </div>

                <div class="topbar__actions">

                    {{-- Notifications --}}
                    <div style="position:relative;">
                        <button class="icon-btn" onclick="toggleNotifications()" aria-label="Notifications">
                            <i class="fa-regular fa-bell"></i>
                            @if(($notificationsCount ?? 0) > 0)
                                <span class="icon-btn__badge" id="notificationBadge">{{ $notificationsCount }}</span>
                            @endif
                        </button>

                        <div class="dropdown" id="notificationDropdown">
                            <div class="dropdown__head">
                                <span>Notifications</span>
                                @if(($notificationsCount ?? 0) > 0)
                                    <button onclick="markAllAsRead()">Tout marquer lu</button>
                                @endif
                            </div>

                            <div>
                                @if(isset($notifications) && $notifications->count() > 0)
                                    @foreach($notifications as $notification)
                                        <div class="dropdown__item {{ $notification->read_at ? '' : 'is-unread' }}"
                                             onclick="markNotificationRead('{{ $notification->id }}')">
                                            <div class="dropdown__item-icon {{ $notification->data['type'] ?? 'info' }}">
                                                <i class="{{ $notification->data['icon'] ?? 'fa-regular fa-bell' }}"></i>
                                            </div>
                                            <div class="dropdown__item-content">
                                                <div class="dropdown__item-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                                <div class="dropdown__item-desc">{{ $notification->data['message'] ?? '' }}</div>
                                                <div class="dropdown__item-time">{{ $notification->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="dropdown__empty">
                                        <i class="fa-regular fa-bell-slash"></i>
                                        <span>Aucune notification</span>
                                    </div>
                                @endif
                            </div>

                            @if(isset($notifications) && $notifications->count() > 0)
                                <div class="dropdown__footer">
                                    <a href="{{ route('notifications.index') }}">Voir tout</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Avatar + User menu --}}
                    <div class="topbar-avatar-wrapper">
                        <div class="topbar-avatar" onclick="toggleUserMenu()">
                            {{ $agenceInitiales }}
                        </div>

                        <div class="dropdown user-dropdown" id="userDropdown">
                            <div class="user-dropdown__head">
                                <div class="user-dropdown__avatar">
                                    {{ $agenceInitiales }}
                                </div>
                                <div class="user-dropdown__name">{{ $agenceNom }}</div>
                                <div class="user-dropdown__email">{{ Auth::user()->email }}</div>
                                @if($estValidee)
                                    <span class="user-dropdown__badge is-verified">
                                        <i class="fa-solid fa-circle-check"></i> Agence vérifiée
                                    </span>
                                @else
                                    <span class="user-dropdown__badge is-pending">
                                        <i class="fa-solid fa-clock"></i> En attente
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('agence.profil') }}" class="user-dropdown__item">
                                <i class="fa-regular fa-user"></i> Profil agence
                            </a>
                            <a href="{{ route('agence.dashboard') }}" class="user-dropdown__item">
                                <i class="fa-regular fa-gauge"></i> Tableau de bord
                            </a>
                            @if(config('abonnement.actif', false))
                                <a href="{{ route('agence.abonnement') }}" class="user-dropdown__item">
                                    <i class="fa-solid fa-award"></i> Abonnement
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="user-dropdown__item is-danger">
                                    <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash messages --}}
            @if(session('success') || session('error') || session('info') || session('warning'))
                <div class="flash-container">
                    @if(session('success'))
                        <div class="flash flash--success" data-auto-dismiss="5000">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ session('success') }}</span>
                            <button class="flash__close" onclick="this.parentElement.remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flash flash--error" data-auto-dismiss="7000">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ session('error') }}</span>
                            <button class="flash__close" onclick="this.parentElement.remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="flash flash--warning" data-auto-dismiss="7000">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ session('warning') }}</span>
                            <button class="flash__close" onclick="this.parentElement.remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="flash flash--info" data-auto-dismiss="5000">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>{{ session('info') }}</span>
                            <button class="flash__close" onclick="this.parentElement.remove()">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Toast container --}}
            <div class="toast-container" id="toastContainer"></div>

            {{-- Contenu --}}
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         BOTTOM NAV (Mobile)
    ═══════════════════════════════════════════ --}}
    <nav class="bottom-nav" role="navigation" aria-label="Navigation principale">
        <div class="bottom-nav__inner">

            @if($estValidee)
                <a href="{{ route('agence.dashboard') }}"
                   class="bottom-nav__item {{ request()->routeIs('agence.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('agence.demandes.index') }}"
                   class="bottom-nav__item {{ request()->routeIs('agence.demandes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-house-circle-check"></i>
                    <span>Besoins</span>
                    @if(($besoinsDisponibles ?? 0) > 0)
                        <span class="bottom-nav__badge">{{ $besoinsDisponibles }}</span>
                    @endif
                </a>

                <a href="{{ route('agence.biens.create') }}" class="bottom-nav__item publier">
                    <i class="fa-solid fa-plus"></i>
                    <span>Publier</span>
                </a>

                <a href="{{ route('agence.propositions.index') }}"
                   class="bottom-nav__item {{ request()->routeIs('agence.propositions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Offres</span>
                </a>

                <a href="{{ route('agence.profil') }}"
                   class="bottom-nav__item {{ request()->routeIs('agence.profil') ? 'active' : '' }}">
                    <i class="fa-solid fa-user"></i>
                    <span>Profil</span>
                </a>
            @else
                <a href="{{ route('home') }}" class="bottom-nav__item">
                    <i class="fa-solid fa-house"></i>
                    <span>Accueil</span>
                </a>

                <a href="{{ route('agence.attente-validation') }}" class="bottom-nav__item active">
                    <i class="fa-solid fa-clock"></i>
                    <span>Validation</span>
                </a>

                <a href="{{ route('agence.profil') }}" class="bottom-nav__item">
                    <i class="fa-solid fa-user"></i>
                    <span>Profil</span>
                </a>
            @endif
        </div>
    </nav>

    <script>
        /* ═══════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════ */
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeSidebar();
                closeAllDropdowns();
            }
        });

        /* ═══════════════════════════════════════════
           DROPDOWNS
        ═══════════════════════════════════════════ */
        function toggleNotifications() {
            const el = document.getElementById('notificationDropdown');
            const isOpen = el.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) el.classList.add('open');
        }

        function toggleUserMenu() {
            const el = document.getElementById('userDropdown');
            const isOpen = el.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) el.classList.add('open');
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
        }

        document.addEventListener('click', e => {
            if (!e.target.closest('.topbar-avatar-wrapper') &&
                !e.target.closest('.icon-btn')) {
                closeAllDropdowns();
            }
        });

        /* ═══════════════════════════════════════════
           NOTIFICATIONS
        ═══════════════════════════════════════════ */
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        function markNotificationRead(id) {
            fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
        }

        function markAllAsRead() {
            fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
        }

        /* ═══════════════════════════════════════════
           TOAST
        ═══════════════════════════════════════════ */
        function showToast(title, message, type = 'info', icon = null) {
            const container = document.getElementById('toastContainer');
            const icons = {
                success: 'fa-regular fa-circle-check',
                warning: 'fa-regular fa-triangle-exclamation',
                info:    'fa-regular fa-circle-info',
                danger:  'fa-regular fa-circle-xmark',
            };
            const colors = {
                success: '#1E7A47',
                warning: '#E65100',
                info:    '#0D47A1',
                danger:  '#C62828',
            };

            const el = document.createElement('div');
            el.className = `toast toast--${type}`;
            el.innerHTML = `
                <div class="toast__icon" style="color:${colors[type] || colors.info}">
                    <i class="${icon || icons[type] || icons.info}"></i>
                </div>
                <div class="toast__content">
                    <div class="toast__title">${title}</div>
                    <div class="toast__message">${message}</div>
                </div>
                <button class="toast__close"><i class="fa-solid fa-xmark"></i></button>
            `;

            el.querySelector('.toast__close').onclick = () => {
                el.classList.add('is-out');
                setTimeout(() => el.remove(), 300);
            };

            container.appendChild(el);

            setTimeout(() => {
                el.classList.add('is-out');
                setTimeout(() => el.remove(), 300);
            }, 6000);
        }

        /* ═══════════════════════════════════════════
           POLLING NOTIFICATIONS
        ═══════════════════════════════════════════ */
        function checkNewNotifications() {
            fetch('/api/notifications/new', {
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.notifications?.length) {
                    data.notifications.forEach(n => {
                        showToast(
                            n.title || 'Nouvelle notification',
                            n.message || '',
                            n.type || 'info',
                            n.icon || null
                        );
                    });
                    if (data.count > 0) {
                        const badge = document.getElementById('notificationBadge');
                        if (badge) badge.textContent = data.count;
                    }
                }
            })
            .catch(() => {});
        }
        setInterval(checkNewNotifications, 30000);

        /* ═══════════════════════════════════════════
           FLASH AUTO-DISMISS
        ═══════════════════════════════════════════ */
        document.querySelectorAll('.flash[data-auto-dismiss]').forEach(flash => {
            const delay = parseInt(flash.dataset.autoDismiss, 10) || 5000;
            setTimeout(() => {
                flash.classList.add('is-out');
                setTimeout(() => flash.remove(), 300);
            }, delay);
        });

        /* ═══════════════════════════════════════════
           INIT
        ═══════════════════════════════════════════ */
        document.addEventListener('DOMContentLoaded', () => {
            console.log('%c DoyaImmo Agence — prêt',
                'color:#B5502A;font-weight:bold;padding:2px 6px;border-radius:4px;background:#FFF4E5;');
        });
    </script>

    @stack('scripts')
</body>
</html>