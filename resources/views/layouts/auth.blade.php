<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DoyaImmo')</title>
    
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
            --teal: #0E7A7A;
            --bg-soft: #F7F9FC;
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

        /* Auth Shell */
        .auth-shell {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        @media (max-width: 860px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }
        }

        /* Visual Side */
        .auth-visual {
            background: var(--ink);
            color: #fff;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-visual::before {
            content: "";
            position: absolute;
            right: -80px;
            bottom: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181,80,42,.2), transparent 70%);
        }

        .auth-visual .brand {
            position: relative;
            z-index: 2;
        }

        .auth-visual .quote {
            font-size: 18px;
            font-weight: 400;
            line-height: 1.7;
            color: #D4D8E0;
            max-width: 400px;
            margin: 40px 0 8px;
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
            margin-top: 40px;
            position: relative;
            z-index: 2;
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

        /* Form Side */
        .auth-form-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: #fff;
        }

        .auth-box {
            max-width: 420px;
            width: 100%;
        }

        /* Brand */
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

        /* Role Toggle - Style moderne */
        .role-toggle {
            display: flex;
            background: #F0F2F5;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 24px;
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
            transition: all 0.3s ease;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .role-toggle a:hover:not(.active) {
            background: rgba(0,0,0,0.04);
        }

        .role-toggle a .role-badge {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 1px 8px;
            border-radius: 20px;
            background: var(--rust-soft);
            color: var(--rust);
            letter-spacing: 0.3px;
        }

        .role-toggle a.active .role-badge {
            background: var(--rust);
            color: #fff;
        }

        .role-toggle a .role-badge.teal {
            background: rgba(14, 122, 122, 0.12);
            color: var(--teal);
        }

        .role-toggle a.active .role-badge.teal {
            background: var(--teal);
            color: #fff;
        }

        /* Typography */
        .auth-box h1 {
            font-family: var(--display);
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 6px;
        }

        .auth-box .sub {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 24px;
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

        /* Choice Cards */
        .choice-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin: 24px 0;
        }

        .choice-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
            border: 2px solid var(--border);
            background: #fff;
            text-align: center;
            min-height: 120px;
        }

        .choice-card:hover {
            border-color: var(--rust);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .choice-card .icon {
            font-size: 28px;
            margin-bottom: 8px;
            color: var(--rust);
        }

        .choice-card .label {
            font-weight: 600;
            color: var(--ink);
            font-size: 15px;
        }

        .choice-card .desc {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .choice-card.active {
            border-color: var(--rust);
            background: var(--rust-soft);
        }

        /* Auth Footer */
        .auth-foot-link {
            text-align: center;
            margin-top: 24px;
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

        /* Flash Messages */
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

        /* Responsive */
        @media (max-width: 480px) {
            .auth-visual {
                padding: 32px 20px;
            }
            
            .auth-visual .quote {
                font-size: 16px;
                margin-top: 24px;
            }
            
            .auth-stats {
                gap: 20px;
                flex-wrap: wrap;
            }
            
            .auth-stats div b {
                font-size: 18px;
            }
            
            .choice-grid {
                grid-template-columns: 1fr;
            }
            
            .auth-box h1 {
                font-size: 20px;
            }

            .role-toggle a {
                font-size: 12px;
                padding: 8px 10px;
                gap: 4px;
            }

            .role-toggle a i {
                font-size: 12px;
            }

            .role-toggle a .role-badge {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

@yield('content')

@stack('scripts')
</body>
</html>