<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Agence — DoyaImmo')</title>

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

        .sidebar-foot {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .plan-card {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .plan-card .tier {
            font-weight: 600;
            font-size: 14px;
            color: #fff;
        }

        .plan-card .desc {
            font-size: 12px;
            color: #8A91A0;
            margin: 4px 0 8px;
        }

        .plan-card a {
            color: var(--rust);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
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
            gap: 20px;
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
            flex-wrap: wrap;
        }

        /* ==================== RECHERCHE ==================== */
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #F7F9FC;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 6px 14px;
            transition: all 0.2s;
            position: relative;
            min-width: 200px;
            max-width: 300px;
        }

        .search-box:focus-within {
            border-color: var(--rust);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
        }

        .search-box input {
            border: none;
            background: none;
            padding: 6px 0;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            min-width: 120px;
            color: var(--ink);
            width: 100%;
        }

        .search-box input::placeholder {
            color: var(--muted);
        }

        .search-box .search-shortcut {
            font-size: 10px;
            color: var(--muted);
            background: var(--border);
            padding: 1px 8px;
            border-radius: 4px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .search-results {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
            padding: 8px 0;
        }

        .search-results.open {
            display: block;
        }

        .search-result-item {
            padding: 10px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.2s;
            border-bottom: 1px solid var(--border);
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: #F7F9FC;
        }

        .search-result-item .result-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .search-result-item .result-icon.besoin {
            background: #E3F2FD;
            color: #0D47A1;
        }

        .search-result-item .result-icon.bien {
            background: #E8F5E9;
            color: #1E7A47;
        }

        .search-result-item .result-icon.agence {
            background: #FFF8E1;
            color: #E65100;
        }

        .search-result-item .result-content {
            flex: 1;
            min-width: 0;
        }

        .search-result-item .result-title {
            font-weight: 600;
            font-size: 13px;
            color: var(--ink);
        }

        .search-result-item .result-desc {
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-result-item .result-tag {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--border);
            padding: 1px 10px;
            border-radius: 20px;
            font-weight: 600;
            flex-shrink: 0;
            letter-spacing: 0.3px;
        }

        .search-empty {
            padding: 30px 20px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        .search-empty i {
            font-size: 28px;
            display: block;
            margin-bottom: 8px;
            opacity: 0.3;
        }

        /* ==================== NOTIFICATIONS ==================== */
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

        .icon-btn .badge-count {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #C62828;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        .icon-btn .dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #C62828;
            border: 2px solid #fff;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        .dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 420px;
            max-width: 90vw;
            max-height: 450px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            z-index: 1000;
        }

        .dropdown.open {
            display: block;
        }

        .dropdown-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
            border-radius: 12px 12px 0 0;
        }

        .dropdown-header button {
            background: none;
            border: none;
            color: var(--rust);
            font-size: 12px;
            cursor: pointer;
            font-weight: 600;
            padding: 4px 8px;
        }

        .dropdown-header button:hover {
            text-decoration: underline;
        }

        .dropdown-item {
            padding: 12px 18px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background: #F7F9FC;
        }

        .dropdown-item.unread {
            background: rgba(181, 80, 42, 0.04);
            border-left: 3px solid var(--rust);
        }

        .dropdown-item.unread:hover {
            background: rgba(181, 80, 42, 0.08);
        }

        .dropdown-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .dropdown-item-icon.info {
            background: #E3F2FD;
            color: #0D47A1;
        }

        .dropdown-item-icon.success {
            background: #E8F5E9;
            color: #1E7A47;
        }

        .dropdown-item-icon.warning {
            background: #FFF8E1;
            color: #E65100;
        }

        .dropdown-item-icon.danger {
            background: #FFEBEE;
            color: #C62828;
        }

        .dropdown-item-content {
            flex: 1;
            min-width: 0;
        }

        .dropdown-item-title {
            font-weight: 600;
            font-size: 13px;
            color: var(--ink);
        }

        .dropdown-item-desc {
            font-size: 12.5px;
            color: var(--text-soft);
            margin-top: 2px;
            line-height: 1.5;
        }

        .dropdown-item-time {
            font-size: 11px;
            color: var(--muted);
            margin-top: 4px;
        }

        .dropdown-item-link {
            color: var(--rust);
            font-weight: 600;
            text-decoration: none;
            font-size: 12px;
        }

        .dropdown-item-link:hover {
            text-decoration: underline;
        }

        .dropdown-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .dropdown-empty i {
            font-size: 32px;
            display: block;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        .dropdown-footer {
            padding: 10px 18px;
            border-top: 1px solid var(--border);
            text-align: center;
            position: sticky;
            bottom: 0;
            background: #fff;
            border-radius: 0 0 12px 12px;
        }

        .dropdown-footer a {
            color: var(--rust);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .dropdown-footer a:hover {
            text-decoration: underline;
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
            cursor: pointer;
        }

        /* ==================== TOAST ==================== */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 380px;
            width: 90%;
        }

        .toast {
            background: #fff;
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border-left: 4px solid var(--rust);
            animation: slideInRight 0.4s ease;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .toast-success {
            border-left-color: #1E7A47;
        }

        .toast-warning {
            border-left-color: #E65100;
        }

        .toast-info {
            border-left-color: #0D47A1;
        }

        .toast-danger {
            border-left-color: #C62828;
        }

        .toast-icon {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .toast-content {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--ink);
        }

        .toast-message {
            font-size: 13px;
            color: var(--text-soft);
            margin-top: 2px;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            flex-shrink: 0;
        }

        .toast-close:hover {
            color: var(--ink);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .toast-removing {
            animation: slideOutRight 0.3s ease forwards;
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

        /* ==================== KPI ==================== */
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

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
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

        /* ==================== BOUTONS ==================== */
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
            font-size: 12.5px;
        }

        .btn-block {
            width: 100%;
            justify-content: center;
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

        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .pagination li {
            display: inline;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            color: var(--text-soft);
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s;
            min-width: 40px;
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

        /* ==================== RESPONSIVE ==================== */

        /* Tablettes et petits écrans */
        @media (max-width: 1024px) {
            .main {
                padding: 20px 24px 30px;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

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
                padding: 16px 20px 24px;
                width: 100%;
            }

            .burger {
                display: block;
            }

            .topbar {
                gap: 12px;
            }

            .top-actions {
                margin-left: 0;
                width: 100%;
                justify-content: flex-start;
                gap: 10px;
            }

            .search-box {
                min-width: 150px;
                flex: 1;
                max-width: 100%;
            }

            .search-box input {
                min-width: 80px;
                font-size: 13px;
            }

            .dropdown {
                width: 340px;
                right: -40px;
            }

            .kpi-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .kpi-card {
                padding: 16px 18px;
            }

            .kpi-value {
                font-size: 22px;
            }

            .page-title {
                font-size: 18px;
            }

            .section-head h2 {
                font-size: 20px;
            }

            .panel {
                padding: 16px 18px;
            }
        }

        /* Mobiles */
        @media (max-width: 600px) {
            .main {
                padding: 12px 14px 20px;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                margin-bottom: 20px;
                padding-bottom: 14px;
            }

            .top-actions {
                flex-wrap: wrap;
                gap: 8px;
            }

            .search-box {
                min-width: 100%;
                max-width: 100%;
            }

            .search-box .search-shortcut {
                display: none;
            }

            .search-box input {
                font-size: 14px;
                min-width: 60px;
            }

            .icon-btn {
                width: 36px;
                height: 36px;
            }

            .icon-btn .badge-count {
                font-size: 9px;
                min-width: 16px;
                height: 16px;
            }

            .topbar-avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .dropdown {
                width: 290px;
                right: -60px;
                max-width: 90vw;
            }

            .dropdown-item {
                padding: 10px 14px;
                gap: 10px;
            }

            .dropdown-item-icon {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .kpi-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .kpi-card {
                padding: 14px 16px;
            }

            .kpi-value {
                font-size: 20px;
            }

            .kpi-label {
                font-size: 12px;
            }

            .kpi-ic {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .page-title {
                font-size: 16px;
            }

            .page-sub {
                font-size: 12px;
            }

            .section-head {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .section-head h2 {
                font-size: 18px;
            }

            .section-head p {
                font-size: 13px;
            }

            .panel {
                padding: 14px 16px;
            }

            .panel-head h3 {
                font-size: 14px;
            }

            .btn {
                font-size: 12.5px;
                padding: 8px 16px;
            }

            .btn-sm {
                padding: 4px 10px;
                font-size: 11.5px;
            }

            .btn-lg {
                padding: 10px 18px;
                font-size: 14px;
            }

            .flash-message {
                font-size: 12px;
                padding: 10px 14px;
            }

            .pagination a,
            .pagination span {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 32px;
            }
        }

        /* Très petits mobiles */
        @media (max-width: 400px) {
            .main {
                padding: 10px 10px 16px;
            }

            .kpi-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .kpi-card {
                padding: 10px 12px;
            }

            .kpi-value {
                font-size: 18px;
            }

            .kpi-label {
                font-size: 11px;
            }

            .top-actions {
                gap: 6px;
            }

            .icon-btn {
                width: 32px;
                height: 32px;
            }

            .icon-btn .badge-count {
                font-size: 8px;
                min-width: 14px;
                height: 14px;
                top: -3px;
                right: -3px;
            }

            .topbar-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .dropdown {
                width: 260px;
                right: -80px;
            }

            .dropdown-item {
                padding: 8px 12px;
                font-size: 12px;
            }

            .page-title {
                font-size: 15px;
            }

            .section-head h2 {
                font-size: 16px;
            }

            .btn {
                font-size: 12px;
                padding: 6px 12px;
            }

            .btn-sm {
                font-size: 11px;
                padding: 4px 8px;
            }
        }

        /* Mode portrait pour mobiles */
        @media (max-width: 480px) and (orientation: portrait) {
            .kpi-grid {
                grid-template-columns: 1fr 1fr;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .filter-bar {
                flex-direction: column;
            }

            .filter-bar select,
            .filter-bar input {
                width: 100%;
                min-width: unset;
            }
        }

        /* Mode paysage pour mobiles */
        @media (max-width: 820px) and (orientation: landscape) {
            .sidebar {
                width: 240px;
            }

            .main {
                padding: 16px 24px;
            }

            .kpi-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Support des écrans très larges */
        @media (min-width: 1600px) {
            .content {
                max-width: 1400px;
            }

            .kpi-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Accessibilité - Réduction de mouvement */
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
            <a href="{{ route('home') }}" class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span>Immo</span></div>
            </a>

            <div class="nav-group-label">Activité</div>
            <div class="navlist">
                <a class="navlink {{ request()->routeIs('agence.dashboard') ? 'active' : '' }}"
                    href="{{ route('agence.dashboard') }}">
                    <i class="ic fa-solid fa-gauge"></i> Tableau de bord
                </a>
                <a class="navlink {{ request()->routeIs('agence.demandes.*') ? 'active' : '' }}"
                    href="{{ route('agence.demandes.index') }}">
                    <i class="ic fa-solid fa-house-circle-check"></i> Besoins disponibles
                    <span class="badge">{{ $besoinsDisponibles ?? 0 }}</span>
                </a>
                <a class="navlink {{ request()->routeIs('agence.propositions.*') ? 'active' : '' }}"
                    href="{{ route('agence.propositions.index') }}">
                    <i class="ic fa-solid fa-file-invoice"></i> Mes offres
                </a>
                <a class="navlink {{ request()->routeIs('agence.rendezvous.*') ? 'active' : '' }}"
                    href="{{ route('agence.rendezvous.index') }}">
                    <i class="ic fa-solid fa-calendar-days"></i> Rendez-vous
                    <span class="badge">{{ $rendezvousAVenir ?? 0 }}</span>
                </a>
                <a class="navlink {{ request()->routeIs('agence.evaluations.*') ? 'active' : '' }}"
                    href="{{ route('agence.evaluations.index') }}">
                    <i class="ic fa-solid fa-star"></i> Avis reçus
                </a>
                <a class="navlink {{ request()->routeIs('agence.historique') ? 'active' : '' }}"
                    href="{{ route('agence.historique') }}">
                    <i class="ic fa-solid fa-clock-rotate-left"></i> Historique
                </a>

                {{-- ✅ CORRECTION : Gérer le cas où l'agence est null --}}
                @php
                $agence = Auth::user()->agence;
                $biensCount = $agence ? $agence->biens()->where('statut', true)->count() : 0;
                @endphp
                <a class="navlink {{ request()->routeIs('agence.biens.*') ? 'active' : '' }}"
                    href="{{ route('agence.biens.index') }}">
                    <i class="ic fa-solid fa-building"></i> Publier vos biens
                    <span class="badge">{{ $biensCount }}</span>
                </a>
            </div>

            <div class="nav-group-label">Compte</div>
            <div class="navlist">
                <a class="navlink {{ request()->routeIs('agence.abonnement') ? 'active' : '' }}"
                    href="{{ route('agence.abonnement') }}">
                    <i class="ic fa-solid fa-award"></i> Abonnement
                </a>
                <a class="navlink {{ request()->routeIs('agence.profil') ? 'active' : '' }}"
                    href="{{ route('agence.profil') }}">
                    <i class="ic fa-solid fa-user"></i> Profil agence
                </a>
                <form action="{{ route('logout') }}" method="POST" style="width:100%;">
                    @csrf
                    <button type="submit" class="navlink" style="width:100%;">
                        <i class="ic fa-solid fa-right-from-bracket"></i> Déconnexion
                    </button>
                </form>
            </div>

            <div class="sidebar-foot">
                <div class="plan-card">
                    <div class="tier">
                        @if(isset($abonnementActuel) && $abonnementActuel)
                        {{ $abonnementActuel->formule->label() ?? 'Standard' }}
                        @else
                        Standard
                        @endif
                    </div>
                    <div class="desc">
                        @if(isset($abonnementActuel) && $abonnementActuel)
                        @php
                        if (!isset($agence)) {
                        $agence = Auth::user()->agence;
                        }

                        $offresUtilisees = $agence->propositions()
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count();

                        $limiteOffres = $abonnementActuel ? $abonnementActuel->formule->limiteOffres() : 0;
                        $limiteAffichage = $limiteOffres === PHP_INT_MAX ? 'Illimité' : $limiteOffres;
                        @endphp
                        Jusqu'à {{ $limiteAffichage }} offres / mois
                        <div style="font-size:10px;color:#8A91A0;margin-top:2px;">
                            {{ $offresUtilisees }} utilisées ce mois
                        </div>
                        @else
                        Aucun abonnement actif
                        @endif
                    </div>
                    <a href="{{ route('agence.abonnement') }}">Voir les plans →</a>
                </div>

                {{-- ✅ CORRECTION : Gérer le cas où l'agence est null --}}
                @php
                $agence = Auth::user()->agence;
                $agenceNom = $agence ? $agence->nom_agence : 'Agence';
                $agenceStatut = $agence ? ($agence->statut_validation ? 'Agence vérifiée' : 'En attente de validation') : 'Non configurée';
                $agenceInitiales = $agence ? strtoupper(substr($agence->nom_agence, 0, 2)) : 'AG';
                @endphp
                <div class="agency-mini">
                    <div class="av" style="background:var(--rust);">
                        {{ $agenceInitiales }}
                    </div>
                    <div>
                        <div class="name">{{ $agenceNom }}</div>
                        <div class="role">{{ $agenceStatut }}</div>
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
                    <div class="page-sub" id="pageSub">@yield('page_sub', 'Vue d\'ensemble de votre activité')</div>
                </div>
                <div class="top-actions">
                    <!-- Recherche -->
                    <div class="search-box" id="searchBox">
                        <i class="fa-solid fa-magnifying-glass" style="color:#9AA1AB; font-size:13px;"></i>
                        <input type="text" id="globalSearch" placeholder="Rechercher..." autocomplete="off">
                        <span class="search-shortcut">⌘K</span>
                        <div class="search-results" id="searchResults"></div>
                    </div>

                    <!-- Notifications -->
                    <div class="icon-btn-wrapper">
                        <button class="icon-btn" id="notificationBtn" onclick="toggleNotifications()">
                            <i class="fa-regular fa-bell" style="color:#20262F;"></i>
                            @if($notificationsCount ?? 0 > 0)
                            <span class="badge-count" id="notificationBadge">{{ $notificationsCount }}</span>
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
                                @if(isset($notifications) && $notifications->count() > 0)
                                @foreach($notifications as $notification)
                                <div class="dropdown-item {{ $notification->read_at ? '' : 'unread' }}" onclick="markNotificationRead('{{ $notification->id }}')">
                                    <div class="dropdown-item-icon {{ $notification->data['type'] ?? 'info' }}">
                                        <i class="{{ $notification->data['icon'] ?? 'fa-regular fa-bell' }}"></i>
                                    </div>
                                    <div class="dropdown-item-content">
                                        <div class="dropdown-item-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                        <div class="dropdown-item-desc">{{ $notification->data['message'] ?? '' }}</div>
                                        <div class="dropdown-item-time">{{ $notification->created_at->diffForHumans() }}</div>
                                        @if(isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" class="dropdown-item-link">Voir les détails →</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="dropdown-empty">
                                    <i class="fa-regular fa-bell-slash"></i>
                                    <span>Aucune notification</span>
                                    <div style="font-size:12px;color:var(--muted);margin-top:4px;">Vous serez informé des nouvelles activités</div>
                                </div>
                                @endif
                            </div>
                            @if(isset($notifications) && $notifications->count() > 0)
                            <div class="dropdown-footer">
                                <a href="{{ route('notifications.index') }}">Voir toutes les notifications</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Avatar -->
                    <div class="topbar-avatar" style="background:var(--rust);">
                        {{ $agenceInitiales ?? 'AG' }}
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

            <!-- Toast Container -->
            <div class="toast-container" id="toastContainer"></div>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // ===================== SIDEBAR =====================
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

        // ===================== RECHERCHE =====================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearch');
            const searchResults = document.getElementById('searchResults');
            const searchBox = document.getElementById('searchBox');
            let debounceTimer;

            document.addEventListener('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.classList.remove('open');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    // Remplacer par votre API
                    const mockResults = [{
                            title: 'Appartement 3 pièces',
                            desc: 'Almadies, Dakar',
                            type: 'bien',
                            icon: 'fa-regular fa-building'
                        },
                        {
                            title: 'Recherche villa 4 pièces',
                            desc: 'Ngor, Dakar',
                            type: 'besoin',
                            icon: 'fa-regular fa-house-circle-check'
                        },
                        {
                            title: 'Teranga Immobilier',
                            desc: 'Agence à Almadies',
                            type: 'agence',
                            icon: 'fa-regular fa-building-columns'
                        },
                    ];

                    const filtered = mockResults.filter(item =>
                        item.title.toLowerCase().includes(query.toLowerCase()) ||
                        item.desc.toLowerCase().includes(query.toLowerCase())
                    );

                    renderSearchResults(filtered);
                }, 300);
            });

            function renderSearchResults(results) {
                if (results.length === 0) {
                    searchResults.innerHTML = `
                        <div class="search-empty">
                            <i class="fa-regular fa-search"></i>
                            Aucun résultat trouvé pour "<strong>${searchInput.value}</strong>"
                        </div>
                    `;
                    searchResults.classList.add('open');
                    return;
                }

                searchResults.innerHTML = results.map(item => `
                    <div class="search-result-item" onclick="window.location.href='#'">
                        <div class="result-icon ${item.type}">
                            <i class="${item.icon}"></i>
                        </div>
                        <div class="result-content">
                            <div class="result-title">${item.title}</div>
                            <div class="result-desc">${item.desc}</div>
                        </div>
                        <span class="result-tag">${item.type}</span>
                    </div>
                `).join('');

                searchResults.classList.add('open');
            }

            document.addEventListener('click', function(e) {
                if (!searchBox.contains(e.target)) {
                    searchResults.classList.remove('open');
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchResults.classList.remove('open');
                    searchInput.blur();
                }
            });
        });

        // ===================== NOTIFICATIONS =====================
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('open');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.icon-btn-wrapper')) {
                document.getElementById('notificationDropdown').classList.remove('open');
            }
        });

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

        // ===================== TOAST =====================
        function showToast(title, message, type = 'info', icon = null) {
            const container = document.getElementById('toastContainer');
            const icons = {
                success: 'fa-regular fa-circle-check',
                warning: 'fa-regular fa-triangle-exclamation',
                info: 'fa-regular fa-circle-info',
                danger: 'fa-regular fa-circle-xmark'
            };

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="toast-icon" style="color:${type === 'success' ? '#1E7A47' : type === 'warning' ? '#E65100' : type === 'danger' ? '#C62828' : '#0D47A1'}">
                    <i class="${icon || icons[type] || icons.info}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.closest('.toast').remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('toast-removing');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 6000);
        }

        // ===================== POLLING =====================
        function checkNewNotifications() {
            fetch('/api/notifications/new', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notification => {
                            showToast(
                                notification.title || 'Nouvelle notification',
                                notification.message || '',
                                notification.type || 'info',
                                notification.icon || null
                            );
                        });

                        if (data.count > 0) {
                            const badge = document.getElementById('notificationBadge');
                            if (badge) {
                                badge.textContent = data.count;
                            }
                        }
                    }
                })
                .catch(() => {});
        }

        setInterval(checkNewNotifications, 30000);

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DoyaImmo - Agence - Notifications prêtes');
        });
    </script>

    @stack('scripts')

</body>

</html>