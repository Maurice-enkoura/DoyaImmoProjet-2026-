<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#F2F2F7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
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

            --ios-bg: #F2F2F7;
            --ios-card: #FFFFFF;
            --ios-blue: #007AFF;
            --ios-label: #000000;
            --ios-label-2: #3C3C43;
            --ios-label-3: #8E8E93;
            --ios-separator: rgba(60, 60, 67, 0.12);
            --ios-radius-lg: 18px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        /* ═══ ANTI-DÉBORDEMENT GLOBAL ═══ */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
            overscroll-behavior-x: none;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            position: relative;
            width: 100%;
        }

        .app, .main, .content, .ios-header, .topbar {
            max-width: 100%;
        }

        img, video, iframe, svg {
            max-width: 100%;
            height: auto;
        }

        /* ═══ APP WRAPPER ═══ */
        .app {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .app::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 260px;
            height: 100%;
            background: var(--ink);
            z-index: 0;
        }

        /* ═══ SIDEBAR ═══ */
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
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, .15); border-radius: 999px; }

        .brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
            margin-bottom: 24px;
            padding: 6px;
            border-radius: 10px;
            transition: background .2s;
        }
        .brand:hover { background: rgba(255, 255, 255, .05); }
        .brand-mark {
            width: 38px; height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            color: #fff;
            font-family: var(--display);
            font-weight: 800; font-size: 18px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 14px rgba(181, 80, 42, .3);
        }
        .brand-name {
            font-family: var(--display);
            font-weight: 700; font-size: 17px;
            color: #fff; letter-spacing: -.3px;
        }
        .brand-name span { color: #d4754a; }

        .nav-group-label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #6A7280;
            margin: 18px 8px 8px;
            font-weight: 700;
        }

        .navlist { display: flex; flex-direction: column; gap: 2px; }

        .navlink {
            position: relative;
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #B9BFCC;
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: all .2s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }
        .navlink:hover { background: rgba(255, 255, 255, .06); color: #fff; }
        .navlink.active { background: rgba(255, 255, 255, .1); color: #fff; font-weight: 600; }
        .navlink.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 20px;
            border-radius: 0 3px 3px 0;
            background: var(--rust);
        }
        .navlink.active .ic { color: var(--rust); }
        .navlink .ic { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0; transition: color .2s; }
        .navlink .badge {
            margin-left: auto;
            background: var(--rust); color: #fff;
            font-size: 11px; font-weight: 700;
            padding: 1px 9px;
            border-radius: 999px;
            flex-shrink: 0;
            font-family: var(--display);
            min-width: 22px;
            text-align: center;
        }

        .plan-card {
            background: rgba(255, 255, 255, .06);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, .08);
        }
        .plan-card .tier {
            font-family: var(--display);
            font-weight: 700; font-size: 13.5px;
            color: #fff;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .plan-card .tier i { color: var(--c-gold); font-size: 12px; }
        .plan-card .desc {
            font-size: 11.5px;
            color: #8A91A0;
            margin: 6px 0 8px;
            line-height: 1.5;
        }
        .plan-card .desc strong { color: #fff; font-weight: 700; font-family: var(--display); }
        .plan-card a { color: var(--rust); font-size: 12px; font-weight: 600; text-decoration: none; }
        .plan-card a:hover { color: #d4754a; }

        .sidebar-foot {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, .06);
        }

        .agency-mini {
            display: flex; align-items: center; gap: 11px;
            padding: 10px;
            border-radius: 11px;
            background: rgba(255, 255, 255, .04);
            transition: background .2s;
        }
        .agency-mini:hover { background: rgba(255, 255, 255, .08); }
        .agency-mini .av {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--display);
            font-weight: 700; font-size: 14px;
            color: #fff;
            flex-shrink: 0;
            letter-spacing: .5px;
        }
        .agency-mini .name {
            font-size: 13px; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 160px;
        }
        .agency-mini .role {
            font-size: 11px; font-weight: 500;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .agency-mini .role i { font-size: 9px; }

        /* ═══ MAIN ═══ */
        .main {
            margin-left: 260px;
            flex: 1;
            padding: 22px 30px 40px;
            min-height: 100vh;
            width: calc(100% - 260px);
            position: relative;
            z-index: 1;
        }

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
        .topbar__left { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0; }
        .burger { display: none; background: none; border: none; cursor: pointer; padding: 6px; border-radius: 8px; transition: background .2s; }
        .burger:hover { background: var(--border); }
        .burger span { display: block; width: 22px; height: 2px; background: var(--ink); margin: 4px 0; transition: .2s; border-radius: 2px; }

        .topbar-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text-soft);
            text-decoration: none;
            font-size: 14px;
            flex-shrink: 0;
            transition: all .2s ease;
            cursor: pointer;
        }
        .topbar-back:hover {
            border-color: var(--rust);
            color: var(--rust);
            transform: translateX(-2px);
            box-shadow: var(--shadow-sm);
        }
        .topbar-back:active { transform: translateX(-1px) scale(.96); }

        .topbar__titles { min-width: 0; }
        .topbar__title {
            font-family: var(--display);
            font-weight: 700; font-size: 19px;
            color: var(--ink); line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .topbar__sub {
            font-size: 12.5px; color: var(--muted);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-top: 2px;
        }
        .topbar__actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

        .icon-btn {
            position: relative;
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-soft);
            cursor: pointer;
            transition: all .2s;
            font-family: inherit;
        }
        .icon-btn:hover { border-color: var(--rust); color: var(--rust); transform: translateY(-1px); box-shadow: var(--shadow-sm); }
        .icon-btn__badge {
            position: absolute;
            top: -3px; right: -3px;
            background: var(--c-danger); color: #fff;
            font-size: 9.5px; font-weight: 800;
            min-width: 18px; height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
            font-family: var(--display);
        }

        .topbar-avatar-wrapper { position: relative; }
        .topbar-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-family: var(--display);
            font-weight: 700; font-size: 14px;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            box-shadow: 0 4px 10px rgba(181, 80, 42, .2);
            letter-spacing: .3px;
        }
        .topbar-avatar:hover { transform: scale(1.05); box-shadow: 0 6px 14px rgba(181, 80, 42, .3); }

        /* ═══ DROPDOWNS ═══ */
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
        @keyframes dropdownIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

        .dropdown__head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            font-weight: 700; font-size: 14px;
            position: sticky; top: 0;
            background: #fff;
            z-index: 1;
            border-radius: var(--radius) var(--radius) 0 0;
            font-family: var(--display);
        }
        .dropdown__head button {
            background: none; border: none;
            color: var(--rust);
            font-size: 11.5px; font-weight: 600;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: inherit;
        }
        .dropdown__head button:hover { background: var(--rust-soft); }

        .dropdown__item {
            position: relative;
            display: flex; gap: 12px;
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
        .dropdown__item.is-unread { background: rgba(181, 80, 42, .04); border-left: 3px solid var(--rust); }
        .dropdown__item.is-unread:hover { background: rgba(181, 80, 42, .08); }

        .dropdown__item-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 15px;
            cursor: pointer;
        }
        .dropdown__item-icon.info    { background: var(--c-info-bg);    color: var(--c-info); }
        .dropdown__item-icon.success { background: var(--c-success-bg); color: var(--c-success); }
        .dropdown__item-icon.warning { background: var(--c-warning-bg); color: var(--c-warning); }
        .dropdown__item-icon.danger  { background: var(--c-danger-bg);  color: var(--c-danger); }

        .dropdown__item-content { flex: 1; min-width: 0; cursor: pointer; }
        .dropdown__item-title { font-weight: 600; font-size: 13px; color: var(--ink); }
        .dropdown__item-desc { font-size: 12.5px; color: var(--text-soft); margin-top: 2px; line-height: 1.5; }
        .dropdown__item-time { font-size: 11px; color: var(--muted); margin-top: 4px; }

        .dropdown__item-close {
            align-self: flex-start;
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
            padding: 6px;
            border-radius: 6px;
            flex-shrink: 0;
            opacity: .5;
            transition: all .15s;
            font-family: inherit;
            line-height: 1;
            z-index: 2;
        }
        .dropdown__item:hover .dropdown__item-close { opacity: 1; }
        .dropdown__item-close:hover { background: var(--c-danger-bg); color: var(--c-danger); }
        .dropdown__item-close:active { transform: scale(.9); }

        .dropdown__empty { padding: 40px 20px; text-align: center; color: var(--muted); }
        .dropdown__empty i { font-size: 32px; display: block; margin-bottom: 12px; opacity: .3; }
        .dropdown__empty span { font-size: 13px; }

        .dropdown__footer {
            padding: 10px 18px;
            border-top: 1px solid var(--border);
            text-align: center;
            position: sticky; bottom: 0;
            background: #fff;
            border-radius: 0 0 var(--radius) var(--radius);
        }
        .dropdown__footer a { color: var(--rust); font-size: 12px; font-weight: 600; text-decoration: none; }
        .dropdown__footer a:hover { text-decoration: underline; }

        .user-dropdown { width: 260px; padding: 0; }
        .user-dropdown__head { padding: 16px 18px; border-bottom: 1px solid var(--border); }
        .user-dropdown__avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rust), #d4754a);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--display);
            font-weight: 700; font-size: 16px;
            margin-bottom: 10px;
            letter-spacing: .3px;
        }
        .user-dropdown__name { font-family: var(--display); font-weight: 700; font-size: 14px; color: var(--ink); }
        .user-dropdown__email { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .user-dropdown__badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10.5px; font-weight: 700;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .user-dropdown__badge.is-verified { background: var(--c-success-bg); color: var(--c-success); }
        .user-dropdown__badge.is-pending { background: var(--c-warning-bg); color: var(--c-warning); }

        .user-dropdown__item {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 18px;
            color: var(--text-soft);
            text-decoration: none;
            font-size: 13px; font-weight: 500;
            transition: background .15s, color .15s;
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            width: 100%;
            text-align: left;
        }
        .user-dropdown__item:hover { background: var(--bg); color: var(--ink); }
        .user-dropdown__item i { width: 16px; text-align: center; font-size: 13px; color: var(--muted); transition: color .15s; }
        .user-dropdown__item:hover i { color: var(--rust); }
        .user-dropdown__item.is-danger { color: var(--c-danger); }
        .user-dropdown__item.is-danger i { color: var(--c-danger); }
        .user-dropdown__item.is-danger:hover { background: var(--c-danger-bg); }

        /* ═══ FLASH / TOAST ═══ */
        .flash-container { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }
        .flash {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px; font-weight: 500;
            border: 1px solid;
            animation: flashIn .3s ease;
            position: relative;
        }
        @keyframes flashIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
        .flash.is-out { animation: flashOut .3s ease forwards; }
        @keyframes flashOut { to { opacity: 0; transform: translateX(100%); height: 0; padding: 0; margin: 0; } }
        .flash--success { background: var(--c-success-bg); color: var(--c-success); border-color: #C8E6C9; }
        .flash--error   { background: var(--c-danger-bg);  color: var(--c-danger);  border-color: #FFCDD2; }
        .flash--info    { background: var(--c-info-bg);    color: var(--c-info);    border-color: #BBDEFB; }
        .flash--warning { background: var(--c-warning-bg); color: var(--c-warning); border-color: #FFE0B2; }
        .flash i:first-child { font-size: 15px; flex-shrink: 0; }
        .flash__close {
            margin-left: auto; background: none; border: none;
            color: currentColor; opacity: .6;
            cursor: pointer; font-size: 14px;
            padding: 4px; border-radius: 6px;
            transition: opacity .15s;
        }
        .flash__close:hover { opacity: 1; }

        .toast-container {
            position: fixed;
            top: 20px; right: 20px;
            z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
            max-width: calc(100vw - 32px);
            width: 380px;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            background: #fff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--rust);
            display: flex; align-items: flex-start; gap: 12px;
            animation: slideInRight .35s ease;
        }
        @keyframes slideInRight { from { transform: translateX(110%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .toast.is-out { animation: slideOutRight .3s ease forwards; }
        @keyframes slideOutRight { to { transform: translateX(110%); opacity: 0; } }
        .toast--success { border-left-color: var(--c-success); }
        .toast--warning { border-left-color: var(--c-warning); }
        .toast--info    { border-left-color: var(--c-info); }
        .toast--danger  { border-left-color: var(--c-danger); }

        .toast__icon { font-size: 19px; flex-shrink: 0; margin-top: 1px; }
        .toast__content { flex: 1; min-width: 0; }
        .toast__title { font-weight: 700; font-size: 13.5px; color: var(--ink); }
        .toast__message { font-size: 12.5px; color: var(--text-soft); margin-top: 2px; line-height: 1.5; }
        .toast__close {
            background: none; border: none;
            color: var(--muted);
            cursor: pointer; font-size: 14px;
            padding: 2px; border-radius: 6px;
            flex-shrink: 0;
        }
        .toast__close:hover { color: var(--ink); background: var(--bg); }

        /* ═══ CONTENT ═══ */
        .content { max-width: 1200px; width: 100%; }
        .view { display: none; }
        .view.active { display: block; }

        /* ═══ OVERLAY ═══ */
        .overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(2px);
            z-index: 99;
            animation: overlayIn .2s ease;
        }
        .overlay.open { display: block; }
        @keyframes overlayIn { from { opacity: 0; } to { opacity: 1; } }

        .bottom-nav { display: none !important; }
        .ios-header { display: none; }

        /* ═══════════════════════════════════════════════════════
           📱 iOS NATIVE (mobile uniquement)
        ═══════════════════════════════════════════════════════ */
        @media (max-width: 820px) {

            html, body {
                overflow-x: hidden !important;
                width: 100%;
                position: relative;
            }

            body { background: var(--ios-bg); }

            .app::before { display: none; }

            .sidebar { transform: translateX(-100%); width: 280px; }
            .sidebar.open { transform: translateX(0); }

            .main {
                margin-left: 0;
                padding: 0;
                width: 100%;
                background: var(--ios-bg);
                overflow-x: hidden;
                max-width: 100vw;
            }

            .burger { display: none; }
            .topbar { display: none; }
            .topbar-back { display: none; }

            .ios-header {
                display: block;
                position: sticky;
                top: 0;
                z-index: 60;
                background: #F2F2F7;
                padding: calc(10px + env(safe-area-inset-top, 0px)) 16px 14px;
                transition: box-shadow .25s ease;
                max-width: 100%;
            }
            .ios-header.is-scrolled {
                box-shadow: 0 0.5px 0 rgba(60, 60, 67, 0.25);
                padding-bottom: 12px;
            }

            .ios-header__top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 18px;
                transition: margin-bottom .25s ease;
                max-width: 100%;
            }
            .ios-header.is-scrolled .ios-header__top { margin-bottom: 0; }

            .ios-header__back {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px; height: 40px;
                background: #fff;
                border: none;
                border-radius: 50%;
                color: var(--ios-blue);
                text-decoration: none;
                font-size: 17px;
                font-weight: 700;
                box-shadow: 0 1px 3px rgba(0,0,0,.06);
                transition: all .15s ease;
                -webkit-tap-highlight-color: transparent;
                flex-shrink: 0;
                cursor: pointer;
            }
            .ios-header__back:active { transform: scale(.92); background: #EFEFF4; }

            .ios-header__brand { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
            .ios-header__brand-mark {
                width: 32px; height: 32px;
                border-radius: 9px;
                background: linear-gradient(135deg, var(--rust), #d4754a);
                color: #fff;
                font-family: var(--display);
                font-weight: 800; font-size: 15px;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 4px 10px rgba(181, 80, 42, .25);
                flex-shrink: 0;
            }
            .ios-header__brand-name {
                font-family: var(--display);
                font-weight: 700; font-size: 17px;
                color: var(--ios-label);
                letter-spacing: -.3px;
                white-space: nowrap; overflow: hidden;
            }
            .ios-header__brand-name span { color: var(--rust); }

            .ios-header__back-title {
                font-family: var(--display);
                font-size: 17px;
                font-weight: 600;
                color: var(--ios-label);
                letter-spacing: -.3px;
                flex: 1;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                padding: 0 8px;
            }

            .ios-header__actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-shrink: 0;
                max-width: 100%;
            }
            .ios-header__icon {
                position: relative;
                width: 40px; height: 40px;
                border-radius: 50%;
                background: #fff;
                border: none;
                display: flex; align-items: center; justify-content: center;
                color: var(--ios-label);
                font-size: 17px;
                cursor: pointer;
                box-shadow: 0 1px 3px rgba(0,0,0,.06);
                -webkit-tap-highlight-color: transparent;
                transition: transform .15s;
                flex-shrink: 0;
            }
            .ios-header__icon:active { transform: scale(.92); }

            .ios-header__dot {
                position: absolute;
                top: 9px; right: 10px;
                width: 8px; height: 8px;
                border-radius: 50%;
                background: var(--rust);
                border: 2px solid #fff;
            }

            .ios-header__avatar {
                width: 40px; height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #FFE4D0, #FFD1B0);
                color: #8B4513;
                display: flex; align-items: center; justify-content: center;
                font-family: var(--display);
                font-size: 14px; font-weight: 700;
                cursor: pointer;
                transition: transform .15s;
                -webkit-tap-highlight-color: transparent;
                flex-shrink: 0;
                text-decoration: none;
            }
            .ios-header__avatar:active { transform: scale(.92); }

            .ios-header__hero {
                overflow: hidden;
                transition: opacity .2s ease, max-height .3s ease, margin .3s ease;
                max-height: 200px;
            }
            .ios-header.is-scrolled .ios-header__hero {
                opacity: 0;
                max-height: 0;
                margin: 0;
            }

            .ios-header__hello {
                display: flex; align-items: center; gap: 8px;
                font-size: 15px; font-weight: 500;
                color: var(--ios-label-2);
                margin-bottom: 8px;
            }

            .ios-header__title {
                font-family: var(--display);
                font-size: 30px; font-weight: 800;
                line-height: 1.1;
                letter-spacing: -1px;
                color: var(--ios-label);
                margin: 0;
            }

            .content {
                padding: 16px 16px 16px;
                overflow-x: hidden;
                max-width: 100%;
                box-sizing: border-box;
            }
            body:has(.ios-form-actions) .content {
                padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
            }

            .flash-container { padding: 0 16px; }
            .flash { border-radius: 14px; border: none; padding: 14px 16px; font-size: 13.5px; }

            #notificationDropdown {
                position: fixed !important;
                top: calc(70px + env(safe-area-inset-top, 0px)) !important;
                right: 16px !important;
                left: 16px !important;
                width: auto !important;
                max-width: none !important;
            }

            .dropdown {
                max-width: calc(100vw - 32px);
            }
        }

        @media (max-width: 480px) {
            .ios-header { padding-left: 14px; padding-right: 14px; }
            .ios-header__title { font-size: 26px; }
            .content { padding: 14px 14px 14px; }
            body:has(.ios-form-actions) .content {
                padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
            }
            .flash-container { padding: 0 14px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }

        /* ═══ TOAST NOTIFICATIONS (REAL-TIME LIVE) ═══ */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 400px;
            width: calc(100vw - 40px);
            pointer-events: none;
        }

        .toast {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.16);
            border-left: 5px solid var(--rust, #B5502A);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
        }

        .toast.is-out {
            transform: translateX(120%);
            opacity: 0;
        }

        @keyframes toastSlideIn {
            from {
                transform: translateY(-20px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .toast--success { border-left-color: #1E7A47; }
        .toast--warning { border-left-color: #E65100; }
        .toast--info    { border-left-color: #0D47A1; }
        .toast--danger  { border-left-color: #C62828; }

        .toast__icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
        .toast__content { flex: 1; min-width: 0; }
        .toast__title { font-weight: 700; font-size: 14px; color: #111827; line-height: 1.3; margin-bottom: 3px; }
        .toast__message { font-size: 12.5px; color: #4B5563; line-height: 1.45; word-break: break-word; }
        .toast__cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            padding: 6px 12px;
            background: rgba(181, 80, 42, 0.1);
            color: var(--rust, #B5502A);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s;
        }
        .toast__cta:hover { background: rgba(181, 80, 42, 0.2); color: var(--rust, #B5502A); }
        .toast__close { background: none; border: none; color: #9CA3AF; cursor: pointer; padding: 4px; font-size: 14px; line-height: 1; transition: color 0.2s; margin-left: 4px; }
        .toast__close:hover { color: #111827; }
    </style>

    @stack('styles')
</head>

<body>

    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <div class="app">

        {{-- ═══ SIDEBAR ═══ --}}
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
                    <a class="navlink {{ request()->routeIs('agence.dashboard') ? 'active' : '' }}" href="{{ route('agence.dashboard') }}">
                        <i class="ic fa-solid fa-gauge"></i> Tableau de bord
                    </a>
                    <a class="navlink {{ request()->routeIs('agence.demandes.*') ? 'active' : '' }}" href="{{ route('agence.demandes.index') }}">
                        <i class="ic fa-solid fa-house-circle-check"></i> Besoins disponibles
                        @if(($besoinsDisponibles ?? 0) > 0)<span class="badge">{{ $besoinsDisponibles }}</span>@endif
                    </a>
                    <a class="navlink {{ request()->routeIs('agence.propositions.*') ? 'active' : '' }}" href="{{ route('agence.propositions.index') }}">
                        <i class="ic fa-solid fa-file-invoice"></i> Mes offres
                    </a>
                    <a class="navlink {{ request()->routeIs('agence.rendezvous.*') ? 'active' : '' }}" href="{{ route('agence.rendezvous.index') }}">
                        <i class="ic fa-solid fa-calendar-days"></i> Rendez-vous
                        @if(($rendezvousAVenir ?? 0) > 0)<span class="badge">{{ $rendezvousAVenir }}</span>@endif
                    </a>
                    <a class="navlink {{ request()->routeIs('agence.evaluations.*') ? 'active' : '' }}" href="{{ route('agence.evaluations.index') }}">
                        <i class="ic fa-solid fa-star"></i> Avis reçus
                    </a>
                    <a class="navlink {{ request()->routeIs('agence.historique') ? 'active' : '' }}" href="{{ route('agence.historique') }}">
                        <i class="ic fa-solid fa-clock-rotate-left"></i> Historique
                    </a>
                    <a class="navlink {{ request()->routeIs('agence.biens.*') ? 'active' : '' }}" href="{{ route('agence.biens.index') }}">
                        <i class="ic fa-solid fa-building"></i> Publier vos biens
                        @if($biensCount > 0)<span class="badge">{{ $biensCount }}</span>@endif
                    </a>
                </nav>

                <div class="nav-group-label">Compte</div>
                <nav class="navlist">
                    @if(config('abonnement.actif', false))
                        <a class="navlink {{ request()->routeIs('agence.abonnement') ? 'active' : '' }}" href="{{ route('agence.abonnement') }}">
                            <i class="ic fa-solid fa-award"></i> Abonnement
                        </a>
                    @endif
                    <a class="navlink {{ request()->routeIs('agence.profil') ? 'active' : '' }}" href="{{ route('agence.profil') }}">
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
                    <a class="navlink {{ request()->routeIs('agence.attente-validation') ? 'active' : '' }}" href="{{ route('agence.attente-validation') }}">
                        <i class="ic fa-solid fa-clock"></i> Validation en cours
                    </a>
                    <a class="navlink" href="{{ route('home') }}" target="_blank">
                        <i class="ic fa-solid fa-house"></i> Accueil
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
                @if(config('abonnement.actif', false) && isset($abonnementActuel))
                    <div class="plan-card">
                        <div class="tier">
                            <i class="fa-solid fa-award"></i>
                            {{-- ✅ CORRECTION : vérifier que $abonnementActuel existe avant d'accéder à formule --}}
                            @if($abonnementActuel && $abonnementActuel->formule)
                                {{ $abonnementActuel->formule->label() }}
                            @else
                                Aucun abonnement
                            @endif
                        </div>
                        <div class="desc">
                            @if($abonnementActuel && $abonnementActuel->formule)
                                @php
                                    $offresUtilisees = $agence->propositions()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
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

        {{-- ═══ MAIN ═══ --}}
        <div class="main">

            @php
                $isDashboard = request()->routeIs('agence.dashboard');
                $pageTitle = trim($__env->yieldContent('page_title', 'Tableau de bord'));

                $currentRoute = request()->route() ? request()->route()->getName() : '';
                $isProfil = request()->routeIs('agence.profil');

                $routeParentMap = [
                    'agence.demandes.show'      => 'agence.demandes.index',
                    'agence.propositions.show'  => 'agence.propositions.index',
                    'agence.rendezvous.show'    => 'agence.rendezvous.index',
                    'agence.biens.show'         => 'agence.biens.index',
                    'agence.evaluations.show'   => 'agence.evaluations.index',
                ];

                if ($isProfil) {
                    $backUrl = route('agence.dashboard');
                } elseif (isset($routeParentMap[$currentRoute])) {
                    $backUrl = route($routeParentMap[$currentRoute]);
                } else {
                    $backUrl = route('agence.dashboard');
                }
            @endphp

            {{-- ═══ iOS HEADER (mobile) ═══ --}}
            <div class="ios-header" id="iosHeader">
                <div class="ios-header__top">

                    @if($isDashboard)
                        <a href="{{ route('home') }}" class="ios-header__brand">
                            <div class="ios-header__brand-mark">D</div>
                            <div class="ios-header__brand-name">Doya<span>Immo</span></div>
                        </a>
                        <div class="ios-header__actions">
                            <button class="ios-header__icon" onclick="toggleNotifications(event)" aria-label="Notifications">
                                <i class="fa-regular fa-bell"></i>
                                @if(($notificationsCount ?? 0) > 0)
                                    <span class="ios-header__dot"></span>
                                @endif
                            </button>
                            <a href="{{ route('agence.profil') }}" class="ios-header__avatar" aria-label="Profil agence">
                                {{ $agenceInitiales }}
                            </a>
                        </div>
                    @else
                        <a href="{{ $backUrl }}" class="ios-header__back" aria-label="Retour">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>

                        <div class="ios-header__back-title">{{ $pageTitle }}</div>

                        <div class="ios-header__actions">
                            <button class="ios-header__icon" onclick="toggleNotifications(event)" aria-label="Notifications">
                                <i class="fa-regular fa-bell"></i>
                                @if(($notificationsCount ?? 0) > 0)
                                    <span class="ios-header__dot"></span>
                                @endif
                            </button>
                            <a href="{{ route('agence.profil') }}" class="ios-header__avatar" aria-label="Profil agence">
                                {{ $agenceInitiales }}
                            </a>
                        </div>
                    @endif
                </div>

                @if($isDashboard)
                    <div class="ios-header__hero">
                        <div class="ios-header__hello">
                            Bonjour {{ Auth::user()->prenom ?? 'Agence' }} 
                        </div>
                        <h1 class="ios-header__title">
                            Mon activité<br>immobilière
                        </h1>
                    </div>
                @endif
            </div>

            {{-- ═══ TOPBAR DESKTOP ═══ --}}
            <div class="topbar">
                <div class="topbar__left">
                    <button class="burger" onclick="toggleSidebar()" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>

                    @unless($isDashboard)
                        <a href="{{ $backUrl }}" class="topbar-back" aria-label="Retour">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    @endunless

                    <div class="topbar__titles">
                        <div class="topbar__title">@yield('page_title', 'Tableau de bord')</div>
                        <div class="topbar__sub">@yield('page_sub', 'Vue d\'ensemble de votre activité')</div>
                    </div>
                </div>

                <div class="topbar__actions">
                    <div style="position:relative;">
                        <button class="icon-btn" onclick="toggleNotifications(event)" aria-label="Notifications">
                            <i class="fa-regular fa-bell"></i>
                            @if(($notificationsCount ?? 0) > 0)
                                <span class="icon-btn__badge" id="notificationBadge">{{ $notificationsCount }}</span>
                            @endif
                        </button>
                        <div class="dropdown" id="notificationDropdownDesktop">
                            <div class="dropdown__head">
                                <span>Notifications</span>
                                @if(($notificationsCount ?? 0) > 0)
                                    <button onclick="markAllAsRead()">Tout marquer lu</button>
                                @endif
                            </div>
                            <div data-notif-list>
                                @if(isset($notifications) && $notifications->count() > 0)
                                    @foreach($notifications as $notification)
                                        <div class="dropdown__item {{ $notification->read_at ? '' : 'is-unread' }}"
                                             data-notif-id="{{ $notification->id }}">
                                            <div class="dropdown__item-icon {{ $notification->data['type'] ?? 'info' }}"
                                                 onclick="markNotificationRead('{{ $notification->id }}')">
                                                <i class="{{ $notification->data['icon'] ?? 'fa-regular fa-bell' }}"></i>
                                            </div>
                                            <div class="dropdown__item-content"
                                                 onclick="markNotificationRead('{{ $notification->id }}')">
                                                <div class="dropdown__item-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                                <div class="dropdown__item-desc">{{ $notification->data['message'] ?? '' }}</div>
                                                <div class="dropdown__item-time">{{ $notification->created_at->diffForHumans() }}</div>
                                            </div>
                                            <button type="button" class="dropdown__item-close"
                                                    onclick="event.stopPropagation(); dismissNotification('{{ $notification->id }}', this)"
                                                    aria-label="Fermer">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                    <div class="dropdown__empty" style="display:none;">
                                        <i class="fa-regular fa-bell-slash"></i>
                                        <span>Aucune notification</span>
                                    </div>
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

                    <div class="topbar-avatar-wrapper">
                        <div class="topbar-avatar" onclick="toggleUserMenu(event)">
                            {{ $agenceInitiales }}
                        </div>
                        <div class="dropdown user-dropdown" id="userDropdownDesktop">
                            <div class="user-dropdown__head">
                                <div class="user-dropdown__avatar">{{ $agenceInitiales }}</div>
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

            {{-- ═══ Dropdown notifications mobile ═══ --}}
            <div class="dropdown" id="notificationDropdown">
                <div class="dropdown__head">
                    <span>Notifications</span>
                    @if(($notificationsCount ?? 0) > 0)
                        <button onclick="markAllAsRead()">Tout marquer lu</button>
                    @endif
                </div>
                <div data-notif-list>
                    @if(isset($notifications) && $notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="dropdown__item {{ $notification->read_at ? '' : 'is-unread' }}"
                                 data-notif-id="{{ $notification->id }}">
                                <div class="dropdown__item-icon {{ $notification->data['type'] ?? 'info' }}"
                                     onclick="markNotificationRead('{{ $notification->id }}')">
                                    <i class="{{ $notification->data['icon'] ?? 'fa-regular fa-bell' }}"></i>
                                </div>
                                <div class="dropdown__item-content"
                                     onclick="markNotificationRead('{{ $notification->id }}')">
                                    <div class="dropdown__item-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                    <div class="dropdown__item-desc">{{ $notification->data['message'] ?? '' }}</div>
                                    <div class="dropdown__item-time">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                                <button type="button" class="dropdown__item-close"
                                        onclick="event.stopPropagation(); dismissNotification('{{ $notification->id }}', this)"
                                        aria-label="Fermer">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endforeach
                        <div class="dropdown__empty" style="display:none;">
                            <i class="fa-regular fa-bell-slash"></i>
                            <span>Aucune notification</span>
                        </div>
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

            <div class="toast-container" id="toastContainer"></div>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        /* ═══ NAVIGATION ═══ */
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('open');
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { closeSidebar(); closeAllDropdowns(); }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const header = document.getElementById('iosHeader');
            if (!header) return;
            const onScroll = () => {
                if (window.scrollY > 8) header.classList.add('is-scrolled');
                else header.classList.remove('is-scrolled');
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        });

        /* ═══ DROPDOWNS ═══ */
        function toggleNotifications(event) {
            if (event) event.stopPropagation();
            const isMobile = window.innerWidth <= 820;
            const id = isMobile ? 'notificationDropdown' : 'notificationDropdownDesktop';
            const el = document.getElementById(id);
            if (!el) return;
            const isOpen = el.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) el.classList.add('open');
        }

        function toggleUserMenu(event) {
            if (event) event.stopPropagation();
            const el = document.getElementById('userDropdownDesktop');
            if (!el) return;
            const isOpen = el.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) el.classList.add('open');
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
        }

        document.addEventListener('click', e => {
            if (e.target.closest('.dropdown') ||
                e.target.closest('.topbar-avatar-wrapper') ||
                e.target.closest('.ios-header__actions') ||
                e.target.closest('.dropdown__item-close')) {
                return;
            }
            closeAllDropdowns();
        });

        /* ═══ NOTIFICATIONS ═══ */
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        function markNotificationRead(id) {
            fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.querySelectorAll(`[data-notif-id="${id}"]`).forEach(el => {
                        el.classList.remove('is-unread');
                    });
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        const n = Math.max(0, parseInt(badge.textContent, 10) - 1);
                        badge.textContent = n;
                        if (n <= 0) badge.style.display = 'none';
                    }
                }
            })
            .catch(() => {});
        }

        function dismissNotification(id, btn) {
            const item = btn.closest('.dropdown__item');
            if (!item) return;

            item.style.transition = 'all .25s ease';
            item.style.opacity = '0';
            item.style.height = '0';
            item.style.paddingTop = '0';
            item.style.paddingBottom = '0';
            item.style.marginTop = '0';
            item.style.marginBottom = '0';
            item.style.overflow = 'hidden';

            if (item.classList.contains('is-unread')) {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    const n = Math.max(0, parseInt(badge.textContent, 10) - 1);
                    badge.textContent = n;
                    if (n <= 0) badge.style.display = 'none';
                }
            }

            setTimeout(() => {
                item.remove();
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    const list = dropdown.querySelector('[data-notif-list]');
                    if (list) {
                        const remaining = list.querySelectorAll('.dropdown__item').length;
                        if (remaining === 0) {
                            const empty = list.querySelector('.dropdown__empty');
                            if (empty) empty.style.display = 'block';
                        }
                    }
                });
            }, 260);
        }

        function markAllAsRead() {
            fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.querySelectorAll('.dropdown__item.is-unread').forEach(el => {
                        el.classList.remove('is-unread');
                    });
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        badge.textContent = '0';
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(() => {});
        }

        /* ═══ TOAST NOTIFICATIONS (LIVE REAL-TIME) ═══ */
        function showToast(title, message, type = 'info', icon = null, link = null) {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }

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
            
            let ctaHtml = '';
            if (link) {
                ctaHtml = `<a href="${link}" class="toast__cta">Consulter <i class="fa-solid fa-arrow-right"></i></a>`;
            }

            el.innerHTML = `
                <div class="toast__icon" style="color:${colors[type] || colors.info}">
                    <i class="${icon || icons[type] || icons.info}"></i>
                </div>
                <div class="toast__content">
                    <div class="toast__title">${title}</div>
                    <div class="toast__message">${message}</div>
                    ${ctaHtml}
                </div>
                <button class="toast__close" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
            `;

            el.querySelector('.toast__close').onclick = () => {
                el.classList.add('is-out');
                setTimeout(() => el.remove(), 300);
            };

            container.appendChild(el);

            setTimeout(() => {
                el.classList.add('is-out');
                setTimeout(() => el.remove(), 300);
            }, 8000);
        }

        /* ═══ REAL-TIME LIVE POLLING & ANTI-DUPLICATION ═══ */
        const seenNotificationIds = new Set();
        let lastNotifCheck = null;

        function updateNotificationBadges(count) {
            document.querySelectorAll('#notificationBadge, .icon-btn__badge').forEach(badge => {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            });
            document.querySelectorAll('.ios-header__dot').forEach(dot => {
                dot.style.display = count > 0 ? 'block' : 'none';
            });
        }

        function prependNotificationToDropdown(n) {
            const dropdownLists = document.querySelectorAll('[data-notif-list]');
            dropdownLists.forEach(list => {
                const emptyMsg = list.querySelector('.dropdown__empty');
                if (emptyMsg) emptyMsg.style.display = 'none';

                const item = document.createElement('div');
                item.className = 'dropdown__item is-unread';
                item.setAttribute('data-notif-id', n.id);
                item.innerHTML = `
                    <div class="dropdown__item-icon ${n.type || 'info'}" onclick="markNotificationRead('${n.id}')">
                        <i class="${n.icon || 'fa-regular fa-bell'}"></i>
                    </div>
                    <div class="dropdown__item-content" onclick="markNotificationRead('${n.id}')">
                        <div class="dropdown__item-title">${n.title || 'Notification'}</div>
                        <div class="dropdown__item-desc">${n.message || ''}</div>
                        <div class="dropdown__item-time">À l'instant</div>
                    </div>
                    <button type="button" class="dropdown__item-close" onclick="event.stopPropagation(); dismissNotification('${n.id}', this)" aria-label="Fermer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                list.insertBefore(item, list.firstChild);
            });
        }

        function checkNewNotifications() {
            let url = '/api/notifications/new';
            if (lastNotifCheck) {
                url += '?last_check=' + encodeURIComponent(lastNotifCheck);
            }
            fetch(url, {
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.timestamp) {
                    lastNotifCheck = data.timestamp;
                }
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(n => {
                        if (seenNotificationIds.has(n.id)) return;
                        seenNotificationIds.add(n.id);

                        showToast(
                            n.title || 'Nouvelle notification',
                            n.message || '',
                            n.type || 'info',
                            n.icon || null,
                            n.link || null
                        );

                        prependNotificationToDropdown(n);

                        window.dispatchEvent(new CustomEvent('doya:new-notification', { detail: n }));
                    });
                }
                if (typeof data.count !== 'undefined') {
                    updateNotificationBadges(data.count);
                }
            })
            .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', () => {
            checkNewNotifications();
            setInterval(checkNewNotifications, 5000);
        });

        /* ═══ FLASH AUTO-DISMISS ═══ */
        document.querySelectorAll('.flash[data-auto-dismiss]').forEach(flash => {
            const delay = parseInt(flash.dataset.autoDismiss, 10) || 5000;
            setTimeout(() => {
                flash.classList.add('is-out');
                setTimeout(() => flash.remove(), 300);
            }, delay);
        });

        document.addEventListener('DOMContentLoaded', () => {
            console.log('%c DoyaImmo Agence — iOS ready',
                'color:#B5502A;font-weight:bold;padding:2px 6px;border-radius:4px;background:#FFF4E5;');
        });
    </script>

    @stack('scripts')
</body>
</html>