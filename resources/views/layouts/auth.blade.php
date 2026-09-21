<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- 🔧 FIX ANTI-FLASH DÉFINITIF : cette règle est volontairement minuscule
         et placée en tout premier, pour s'appliquer quasi instantanément —
         AVANT le gros bloc <style> plus bas et avant tout CSS externe.
         Tant que le JS n'a pas retiré ce masquage, RIEN n'est visible,
         quel que soit le temps que prend le reste de la page à charger. -->
    <style>html{visibility:hidden}</style>
    <noscript><style>html{visibility:visible !important}</style></noscript>

    <title>@yield('title', 'DoyaImmo')</title>

    <!-- Chargement non-bloquant : évite que la page attende ce CDN externe
         avant de pouvoir peindre le CSS critique inline juste en dessous.
         C'est ce blocage qui causait le flash de page "brute" sur mobile
         (connexion plus lente = attente plus longue = flash plus visible). -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>

    <!-- ===== STYLES CRITIQUES ANTI-FLASH ===== -->
    <style>
        /* ==========================================================
           CHAÎNE FLEX PROPRE
        ========================================================== */
        html, body {
            margin: 0;
            padding: 0;
            background: #F7F9FC;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--ink);
            line-height: 1.6;
        }

        /* ==========================================================
           ÉCRAN DE CHARGEMENT (anti-flash)
        ========================================================== */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #F7F9FC;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        #loader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--border, #E8ECF0);
            border-top-color: var(--rust, #B5502A);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        #auth-app {
            opacity: 0;
            transition: opacity 0.4s ease;
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
        }

        #auth-app.ready {
            opacity: 1;
        }

        /* ==========================================================
           VARIABLES
        ========================================================== */
        :root {
            --rust: #B5502A;
            --rust-soft: rgba(181, 80, 42, 0.12);
            --ink: #1A1D26;
            --surface: #FFFFFF;
            --border: #E8ECF0;
            --text-soft: #4A5260;
            --muted: #8A91A0;
            --bg-soft: #F7F9FC;
            --radius: 16px;
            --display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ==========================================================
           AUTH SHELL
        ========================================================== */
        .auth-shell {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: stretch;
            flex: 1 0 auto;
            min-height: 100vh;
            min-height: 100dvh;
        }

        @media (max-width: 860px) {
            .auth-shell {
                grid-template-columns: 1fr;
                flex: none;
            }
        }

        /* ==========================================================
           VISUAL SIDE
        ========================================================== */
        .auth-visual {
            background: var(--ink);
            color: #fff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .auth-visual .brand {
            position: relative;
            z-index: 2;
            margin-bottom: auto;
        }

        .auth-visual .quote {
            font-size: 18px;
            font-weight: 400;
            line-height: 1.7;
            color: #D4D8E0;
            max-width: 400px;
            margin: 30px 0 8px;
            position: relative;
            z-index: 2;
        }

        .auth-visual .quote-by {
            font-size: 13px;
            color: #8A91A0;
            position: relative;
            z-index: 2;
        }

        .auth-stats {
            display: flex;
            gap: 40px;
            margin-top: 30px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .auth-stats div b {
            display: block;
            font-size: 22px;
            font-family: var(--display);
            font-weight: 700;
        }

        .auth-stats div span {
            font-size: 12.5px;
            color: #8A91A0;
        }

        .auth-visual-footer {
            font-size: 12px;
            color: #6A7280;
            margin-top: auto;
            position: relative;
            z-index: 2;
            padding-top: 20px;
        }

        .auth-visual-footer i {
            color: #6A7280;
        }

        @media (max-width: 860px) {
            .auth-visual {
                padding: 32px 24px;
                justify-content: flex-start;
            }

            .auth-visual .quote {
                font-size: 16px;
                margin-top: 20px;
            }

            .auth-stats {
                gap: 20px;
                margin-top: 20px;
            }

            .auth-stats div b {
                font-size: 18px;
            }

            .auth-visual .brand {
                margin-bottom: 0;
            }
        }

        @media (max-width: 480px) {
            .auth-visual {
                padding: 20px 16px;
            }

            .auth-visual .quote {
                font-size: 15px;
                margin-top: 16px;
            }

            .auth-stats {
                gap: 16px;
                margin-top: 16px;
            }

            .auth-stats div b {
                font-size: 16px;
            }

            .auth-stats div span {
                font-size: 11px;
            }
        }

        /* ==========================================================
           FORM SIDE
        ========================================================== */
        .auth-form-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: #fff;
        }

        @media (max-width: 860px) {
            .auth-form-side {
                padding: 32px 20px;
            }
        }

        .auth-box {
            max-width: 420px;
            width: 100%;
            padding: 10px 0;
        }

        @media (max-width: 860px) {
            .auth-box {
                max-width: 100%;
                padding: 0;
            }
        }

        /* ==========================================================
           BRAND
        ========================================================== */
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
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
            flex-shrink: 0;
        }

        .brand-name {
            font-family: var(--display);
            font-weight: 700;
            font-size: 18px;
        }

        .brand-name span {
            color: var(--rust);
        }

        .auth-visual .brand-name {
            color: #fff;
        }

        /* ==========================================================
           ROLE TOGGLE
        ========================================================== */
        .role-toggle {
            display: flex;
            background: #F0F2F5;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 20px;
            position: relative;
        }

        .role-toggle a {
            flex: 1;
            text-align: center;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-soft);
            text-decoration: none;
            transition: background-color 0.15s, color 0.15s;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .role-toggle a i {
            font-size: 14px;
        }

        .role-toggle a.active {
            background: #fff;
            color: var(--ink);
        }

        .role-toggle a:hover:not(.active) {
            background: rgba(0,0,0,0.04);
        }

        /* ==========================================================
           TYPOGRAPHIE
        ========================================================== */
        .auth-box h1 {
            font-family: var(--display);
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 4px;
        }

        .auth-box .sub {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 20px;
        }

        /* ==========================================================
           BOUTONS
        ========================================================== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
            text-align: center;
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

        .btn-rust:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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

        .btn-block {
            width: 100%;
            justify-content: center;
        }

        .btn-lg {
            padding: 14px 28px;
            font-size: 15px;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 12.5px;
        }

        /* ==========================================================
           CHOICE CARDS
        ========================================================== */
        .choice-container {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 24px;
        }

        .choice-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border: 1px solid var(--border);
            border-radius: 14px;
            text-decoration: none;
            transition: border-color 0.15s, background-color 0.15s;
            background: #fff;
            position: relative;
            cursor: pointer;
        }

        .choice-card:hover {
            border-color: var(--rust);
            background: #FAFBFC;
        }

        .choice-card .choice-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            font-size: 18px;
            flex-shrink: 0;
            background: #F5F5F7;
            color: var(--muted);
        }

        .choice-card .choice-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .choice-card .choice-title {
            font-family: var(--display);
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.3;
        }

        .choice-card .choice-desc {
            font-size: 13px;
            color: var(--muted);
        }

        .choice-card .choice-arrow {
            color: var(--muted);
            font-size: 14px;
            flex-shrink: 0;
        }

        .choice-card:hover .choice-arrow {
            color: var(--rust);
        }

        /* ==========================================================
           AUTH DIVIDER
        ========================================================== */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 16px 0;
            color: var(--muted);
            font-size: 13px;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ==========================================================
           FLASH MESSAGES (global)
        ========================================================== */
        .flash-container {
            max-width: 480px;
            width: 100%;
            margin: 16px auto;
            padding: 0 16px;
        }

        .flash-message {
            padding: 10px 16px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-size: 13.5px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.5;
        }

        .flash-message:last-child { margin-bottom: 0; }

        .flash-message i {
            font-size: 14px;
            margin-top: 2px;
            flex-shrink: 0;
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

        /* ==========================================================
           FORM FIELDS
        ========================================================== */
        .field {
            margin-bottom: 14px;
        }

        .field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-soft);
            margin-bottom: 4px;
        }

        .field input,
        .field textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            font-family: inherit;
            transition: border-color 0.15s;
            background: #fff;
        }

        .field input:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--rust);
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: #B0B8C4;
        }

        .field input.is-invalid,
        .field textarea.is-invalid {
            border-color: #C62828;
        }

        .field-hint {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        .error-message {
            display: block;
            color: #C62828;
            font-size: 12px;
            margin-top: 4px;
        }

        .required {
            color: var(--rust);
            font-weight: 600;
            margin-left: 2px;
        }

        /* ==========================================================
           PASSWORD WRAPPER
        ========================================================== */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 44px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 4px;
            font-size: 15px;
            transition: color 0.15s;
        }

        .toggle-password:hover {
            color: var(--text-soft);
        }

        /* ==========================================================
           CHECKBOX
        ========================================================== */
        .check-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: var(--text-soft);
            cursor: pointer;
        }

        .check-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            min-width: 18px;
            margin-top: 2px;
            cursor: pointer;
            accent-color: var(--rust);
        }

        .check-row .link {
            color: var(--rust);
            font-weight: 600;
            text-decoration: none;
        }

        .check-row .link:hover {
            text-decoration: underline;
        }

        /* ==========================================================
           AUTH FOOT LINK
        ========================================================== */
        .auth-foot-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--text-soft);
        }

        .auth-foot-link a {
            color: var(--rust);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-foot-link a:hover {
            text-decoration: underline;
        }

        /* ==========================================================
           FORM SECTION
        ========================================================== */
        .form-section {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--display);
            font-size: 15px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 14px;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #F5F5F7;
            color: var(--text-soft);
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .optional-badge {
            font-size: 11px;
            font-weight: 500;
            color: var(--muted);
            background: var(--border);
            padding: 2px 10px;
            border-radius: 20px;
            margin-left: auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 480px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .conditions-field {
            margin: 16px 0;
        }

        /* ==========================================================
           FORMULAIRES AGENCE
        ========================================================== */
        .form-group {
            margin-bottom: 22px;
            padding-bottom: 22px;
            border-bottom: 1px solid var(--border);
        }

        .form-group:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--display);
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 14px;
        }

        .form-label .label-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #F5F5F7;
            color: var(--muted);
            font-size: 14px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 600px) {
            .row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        .col {
            display: flex;
            flex-direction: column;
        }

        /* ==========================================================
           DOCUMENTS
        ========================================================== */
        .documents-group {
            background: #F5F5F7;
            padding: 14px 18px 18px;
            border-radius: 12px;
        }

        .documents-group .form-label {
            margin-bottom: 10px;
        }

        .document-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        @media (max-width: 600px) {
            .document-grid {
                grid-template-columns: 1fr;
            }
        }

        .document-item {
            position: relative;
        }

        .document-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.15s;
            font-size: 13px;
        }

        .document-label:hover {
            border-color: #d8d8d8;
        }

        .document-label i {
            font-size: 18px;
            color: var(--muted);
            width: 22px;
            text-align: center;
        }

        .document-label div {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .document-label span {
            font-weight: 600;
            color: var(--text);
        }

        .document-label small {
            font-size: 11px;
            color: var(--muted);
        }

        .document-item input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .document-item.has-file .document-label {
            border-color: #1E7A47;
            background: rgba(30, 122, 71, 0.04);
        }

        .document-help {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            font-size: 12px;
            color: var(--muted);
        }

        .document-help i {
            color: var(--muted);
        }

        /* ==========================================================
           CONDITIONS
        ========================================================== */
        .conditions {
            margin: 20px 0 16px;
        }

        .checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: var(--text-soft);
            cursor: pointer;
        }

        .checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            min-width: 18px;
            margin-top: 1px;
            cursor: pointer;
            accent-color: var(--rust);
        }

        .checkbox .link {
            color: var(--rust);
            font-weight: 600;
            text-decoration: none;
        }

        .checkbox .link:hover {
            text-decoration: underline;
        }

        /* ==========================================================
           BOUTON SUBMIT
        ========================================================== */
        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            background: var(--rust);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .btn-submit:hover {
            background: #9A4523;
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-submit i {
            font-size: 16px;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 12px;
            font-size: 12px;
            color: var(--muted);
        }

        .form-footer i {
            color: var(--muted);
        }

        /* ==========================================================
           RESPONSIVE
        ========================================================== */
        @media (max-width: 860px) {
            .auth-box {
                max-width: 100%;
                padding: 0;
            }
        }

        @media (max-width: 480px) {
            .auth-box h1 {
                font-size: 20px;
            }

            .auth-box .sub {
                font-size: 13px;
                margin-bottom: 16px;
            }

            .role-toggle a {
                font-size: 12px;
                padding: 8px 10px;
                gap: 4px;
            }

            .role-toggle a i {
                font-size: 12px;
            }

            .field input,
            .field textarea {
                font-size: 16px;
                padding: 10px 12px;
            }

            .btn {
                font-size: 14px;
                padding: 10px 16px;
            }

            .btn-lg {
                padding: 12px 20px;
                font-size: 14px;
            }

            .auth-form-side {
                padding: 20px 16px;
            }

            .flash-message {
                font-size: 13px;
                padding: 8px 14px;
            }

            .choice-card {
                padding: 12px 16px;
                gap: 12px;
            }

            .choice-card .choice-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
                border-radius: 10px;
            }

            .choice-card .choice-title {
                font-size: 15px;
            }

            .choice-card .choice-desc {
                font-size: 12px;
            }

            .form-section {
                margin-bottom: 16px;
                padding-bottom: 16px;
            }

            .section-title {
                font-size: 14px;
                margin-bottom: 12px;
            }

            .step-badge {
                width: 24px;
                height: 24px;
                font-size: 11px;
            }

            .documents-group {
                padding: 12px 14px 14px;
            }

            .document-label {
                padding: 8px 12px;
                font-size: 12px;
            }

            .document-label i {
                font-size: 16px;
            }

            .btn-submit {
                font-size: 14px;
                padding: 12px 20px;
            }
        }

        @media (max-width: 380px) {
            .auth-box h1 {
                font-size: 18px;
            }

            .role-toggle {
                flex-direction: column;
                gap: 4px;
            }

            .role-toggle a {
                padding: 8px 12px;
            }
        }

        /* ==========================================================
           ANTI-ZOOM MOBILE
        ========================================================== */
        @media (max-width: 768px) {
            .field input,
            .field textarea,
            .field select,
            .password-wrapper input {
                font-size: 16px !important;
            }

            input::placeholder,
            textarea::placeholder {
                font-size: 14px !important;
            }
        }

        /* ==========================================================
           ACCESSIBILITY
        ========================================================== */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ==========================================================
           SCROLLBAR
        ========================================================== */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-soft);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--muted);
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- ===== ÉCRAN DE CHARGEMENT ===== -->
<div id="loader">
    <div class="loader-spinner"></div>
</div>

<!-- ===== FLASH MESSAGES (global) ===== -->
@if(session('success') || session('error') || session('info') || session('warning'))
    <div class="flash-container">
        @if(session('success'))
            <div class="flash-message flash-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flash-message flash-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="flash-message flash-info">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="flash-message flash-info">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
    </div>
@endif

<!-- ===== CONTENU PRINCIPAL AVEC ANTI-FLASH ===== -->
<div id="auth-app">

@yield('content')

</div>

<!-- ==================== TOGGLE CLIENT / AGENCE + LOADER ==================== -->
<script>
(function () {
    'use strict';

    // ============================================
    // ANTI-FLASH : CACHER LE LOADER
    // ============================================
    function showContent() {
        // 🔧 Lève le masquage anti-flash posé tout en haut du <head>.
        // C'est CE moment précis, décidé par le JS, qui révèle la page —
        // jamais avant, quel que soit le temps de chargement du CSS/réseau.
        document.documentElement.style.visibility = 'visible';

        var loader = document.getElementById('loader');
        if (loader) {
            loader.classList.add('hidden');
        }

        var app = document.getElementById('auth-app');
        if (app) {
            app.classList.add('ready');
        }

        var shell = document.querySelector('.auth-shell');
        if (shell) {
            shell.style.opacity = '1';
        }
    }

    if (document.readyState === 'complete') {
        setTimeout(showContent, 100);
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(showContent, 150);
        });
        window.addEventListener('load', function() {
            setTimeout(showContent, 200);
        });
    }

    setTimeout(showContent, 800);

    // ============================================
    // ROLE TOGGLE (navigation client ↔ agence)
    // ============================================
    function initRoleToggle() {
        document.querySelectorAll('.role-toggle a').forEach(function (link) {
            link.removeEventListener('click', handleToggleClick);
            link.addEventListener('click', handleToggleClick);
        });
    }

    function handleToggleClick(e) {
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();

        var url = this.getAttribute('href');
        var shell = document.querySelector('.auth-shell');

        if (!shell || shell.dataset.loading === '1') return;

        shell.dataset.loading = '1';
        shell.style.opacity = '0.3';

        var loader = document.getElementById('loader');
        if (loader) {
            loader.classList.remove('hidden');
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newShell = doc.querySelector('.auth-shell');
                var newTitle = doc.querySelector('title');

                if (newShell) {
                    shell.replaceWith(newShell);
                    if (newTitle) document.title = newTitle.textContent;
                    if (history && history.pushState) {
                        history.pushState({ authUrl: url }, '', url);
                    }
                    initRoleToggle();

                    var loader = document.getElementById('loader');
                    if (loader) {
                        setTimeout(function() {
                            loader.classList.add('hidden');
                        }, 300);
                    }

                    newShell.style.opacity = '1';
                    newShell.dataset.loading = '';
                } else {
                    window.location.href = url;
                }
            })
            .catch(function () {
                window.location.href = url;
            });
    }

    window.addEventListener('popstate', function () {
        var url = window.location.href;
        var shell = document.querySelector('.auth-shell');

        if (shell) {
            shell.style.opacity = '0.3';
        }

        var loader = document.getElementById('loader');
        if (loader) {
            loader.classList.remove('hidden');
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newShell = doc.querySelector('.auth-shell');
                var newTitle = doc.querySelector('title');

                if (newShell) {
                    if (shell) shell.replaceWith(newShell);
                    if (newTitle) document.title = newTitle.textContent;
                    initRoleToggle();

                    var loader = document.getElementById('loader');
                    if (loader) {
                        setTimeout(function() {
                            loader.classList.add('hidden');
                        }, 300);
                    }

                    newShell.style.opacity = '1';
                    newShell.dataset.loading = '';
                } else {
                    window.location.href = url;
                }
            })
            .catch(function () {
                window.location.href = url;
            });
    });

    document.addEventListener('DOMContentLoaded', initRoleToggle);

    // ============================================
    // 🔧 FIX FLASH HTML AU RAFRAÎCHISSEMENT
    // ============================================
    // Le toggle Client/Agence remplace .auth-shell via fetch + replaceWith,
    // sans jamais toucher au <head>/<style>. Si le navigateur restaure
    // ensuite cet état depuis son cache mémoire (bfcache) au moment d'un
    // rafraîchissement, on voit apparaître brièvement ce DOM "nu" avant
    // que la vraie page (re-téléchargée depuis le serveur, avec tout son
    // CSS) ne s'affiche. On force donc un vrai rechargement serveur dans
    // ce cas précis, pour ne jamais montrer cet état intermédiaire.
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
})();
</script>

@stack('scripts')
</body>
</html>